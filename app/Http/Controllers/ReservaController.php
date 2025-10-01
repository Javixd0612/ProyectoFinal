<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reserva;
use App\Models\Consola;
use Carbon\Carbon;

class ReservaController extends Controller
{
    public function index()
    {
        $consolas = Consola::all();
        $misReservas = Auth::user()->reservas()->orderBy('start_at', 'desc')->get();

        return view('reserva.index', compact('consolas', 'misReservas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'consola_id' => 'required|exists:consolas,id',
            'fecha'      => 'required|date',
            'hora'       => 'required',
            'horas'      => 'required|integer|min:1|max:3',
        ]);

        $consola = Consola::findOrFail($request->consola_id);

        $startAt = Carbon::parse($request->fecha . ' ' . $request->hora);
        $horasEntero = (int) $request->horas;
        $endAt = (clone $startAt)->addHours($horasEntero);

        $now = Carbon::now();
        if ($startAt->lt($now)) {
            return back()->withErrors(['fecha' => 'No puedes reservar en una fecha/hora pasada.'])->withInput();
        }

        if (Reserva::overlaps($consola->id, $startAt, $endAt)) {
            return back()->withErrors(['conflict' => 'La consola ya está reservada y pagada en ese horario.'])->withInput();
        }

        $precioTotal = round($consola->precio_hora * $horasEntero, 2);

        $reserva = Reserva::create([
            'user_id' => Auth::id(),
            'consola_id' => $consola->id,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'horas' => $horasEntero,
            'precio_total' => $precioTotal,
            'status' => 'pending',
        ]);

        return redirect()->route('reserva.index')->with('success', 'Reserva creada. Recuerda pagar para confirmar.');
    }

    public function edit(Reserva $reserva)
    {
        if ($reserva->user_id !== Auth::id() || $reserva->status !== 'pending') {
            return redirect()->route('reserva.index')->withErrors('No puedes editar esta reserva.');
        }

        $consolas = Consola::all();

        return view('reserva.edit', compact('reserva', 'consolas'));
    }

    public function update(Request $request, Reserva $reserva)
    {
        if ($reserva->user_id !== Auth::id() || $reserva->status !== 'pending') {
            return redirect()->route('reserva.index')->withErrors('No puedes editar esta reserva.');
        }

        $request->validate([
            'consola_id' => 'required|exists:consolas,id',
            'fecha'      => 'required|date',
            'hora'       => 'required',
            'horas'      => 'required|integer|min:1|max:3',
        ]);

        $consola = Consola::findOrFail($request->consola_id);
        $startAt = Carbon::parse($request->fecha . ' ' . $request->hora);
        $horasEntero = (int) $request->horas;
        $endAt = (clone $startAt)->addHours($horasEntero);

        $now = Carbon::now();
        if ($startAt->lt($now)) {
            return back()->withErrors(['fecha' => 'No puedes actualizar a una fecha/hora pasada.'])->withInput();
        }

        if (Reserva::where('id', '!=', $reserva->id)
            ->where('consola_id', $consola->id)
            ->where('status', 'paid')
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt)
            ->exists()
        ) {
            return back()->withErrors(['conflict' => 'Horario no disponible (existente reserva pagada).'])->withInput();
        }

        $reserva->update([
            'consola_id' => $consola->id,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'horas' => $horasEntero,
            'precio_total' => round($consola->precio_hora * $horasEntero, 2),
        ]);

        return redirect()->route('reserva.index')->with('success', 'Reserva actualizada.');
    }

    public function destroy(Reserva $reserva)
    {
        if ($reserva->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        if (Auth::user()->isAdmin()) {
            $reserva->delete();
            return back()->with('success', 'Reserva eliminada por admin.');
        }

        if ($reserva->status === 'paid') {
            return back()->withErrors('No puedes cancelar una reserva pagada. Contacta al admin.');
        }

        $reserva->update(['status' => 'canceled']);
        return back()->with('success', 'Reserva cancelada.');
    }

    /**
     * Vista sandbox de pago (simulación estilo "captcha" y factura).
     * Ruta: GET /reserva/{reserva}/sandbox
     */
    public function sandbox(Reserva $reserva)
    {
        // seguridad: solo el dueño puede ver su sandbox y solo si está pending
        if ($reserva->user_id !== Auth::id()) {
            abort(403);
        }

        if ($reserva->status !== 'pending') {
            return redirect()->route('reserva.index')->withErrors('Reserva no está en estado pendiente.');
        }

        // cargamos la consola relacionada
        $consola = $reserva->consola;

        return view('reserva.sandbox', compact('reserva', 'consola'));
    }

    public function pay(Reserva $reserva)
    {
        if ($reserva->user_id !== Auth::id()) abort(403);
        if ($reserva->status !== 'pending') return back()->withErrors('Reserva no está en estado pendiente.');

        // Si en un futuro guardas method_used puedes leerlo aquí:
        // $method = request()->input('method_used', null);

        $reserva->update(['status' => 'paid']);

        return redirect()->route('reserva.index')->with('success', 'Pago simulado: reserva marcada como pagada.');
    }

    /* ---------- ADMIN ---------- */

    public function adminIndex(Request $request)
    {
        // Aseguramos que solo admin acceda
        if (! Auth::user() || ! Auth::user()->isAdmin()) {
            abort(403);
        }

        // Parámetros de búsqueda / filtros
        $q = $request->query('q', null);
        $status = $request->query('status', 'all');
        $consolaId = $request->query('consola_id', null);
        $perPage = 8;

        // Query base
        $query = Reserva::with(['user', 'consola'])->orderBy('start_at', 'desc');

        // Filtro status
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filtro por consola id
        if (!empty($consolaId)) {
            $query->where('consola_id', $consolaId);
        }

        // Búsqueda
        if (!empty($q)) {
            $term = '%'.$q.'%';
            $query->where(function($sub) use ($term) {
                $sub->whereHas('user', function($u) use ($term) {
                    $u->where('name', 'like', $term)
                      ->orWhere('email', 'like', $term);
                })
                ->orWhereHas('consola', function($c) use ($term) {
                    $c->where('nombre', 'like', $term);
                });
            });
        }

        // Paginamos (manteniendo query string)
        $reservas = $query->paginate($perPage)->appends($request->query());

        // Estadísticas (sobre el conjunto completo sin paginar)
        $totalGanancias = Reserva::where('status', 'paid')->sum('precio_total');
        $totalPendientes = Reserva::where('status', 'pending')->sum('precio_total');
        $pagadas = Reserva::where('status', 'paid')->count();
        $pendientes = Reserva::where('status', 'pending')->count();

        // Consolas para el filtro y la sección de precios
        $consolas = Consola::all();

        // Si existe la vista blade oficial, la usamos (recomendado)
        if (view()->exists('admin.reservas')) {
            return view('admin.reservas', compact(
                'reservas', 'consolas', 'totalGanancias', 'totalPendientes', 'pagadas', 'pendientes', 'q', 'status', 'consolaId'
            ));
        }

        // --- FALLBACK HTML corregido (menu left + profile fixed outside container) ---
        $css1 = asset('css/app_reservas.css');
        $css2 = asset('css/dashboard.css');
        $csrf = csrf_token();
        $user = auth()->user();

        $html = '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
        $html .= '<title>Panel Admin - Reservas</title>';
        $html .= '<link rel="stylesheet" href="'.$css1.'">';
        $html .= '<link rel="stylesheet" href="'.$css2.'">';
        $html .= '<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>';

        // estilos inline (ajustes específicos para este fallback)
        $html .= '<style>
            :root{ --nav-height:64px; --neon-cyan:#00ffcc; }
            html,body{height:100%;background:#000 !important;color:#fff;margin:0;padding:0;font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;}
            .reserved-nav-space{height:var(--nav-height); width:100%;}
            .gamer-nav { position:fixed; top:0; left:0; right:0; z-index:120; box-shadow:0 6px 18px rgba(0,0,0,0.75); background:rgba(8,8,8,0.9); }
            /* menu left aligned inside container */
            .nav-inner { display:flex; align-items:center; justify-content:flex-start; gap: 24px; }
            .container{max-width:1100px;margin:0 auto;padding:1.25rem;}
            /* fixed profile OUTSIDE the container (corner) */
            .fixed-profile { position: fixed; right: 18px; top: 10px; z-index:130; display:flex; align-items:center; gap:0.6rem; }
            .gamer-menu { display:flex; gap:1.6rem; list-style:none; margin:0; padding:0; }
            .gamer-link { color:var(--neon-cyan); text-decoration:none; text-transform:uppercase; font-weight:700; border-radius:8px; padding:0.6rem 1rem; background:transparent; }
            .gamer-link:hover { background: rgba(0,255,204,0.04); color:#fff; transform:translateY(-2px); }
            .gamer-btn-rect { display:inline-flex; align-items:center; gap:8px; padding:0.45rem 0.85rem; border-radius:10px; background: rgba(255,255,255,0.02); color:var(--neon-cyan); border: 2px solid rgba(0,255,204,0.14); }
            .dropdown-gamer { position:absolute; right:0; margin-top:.6rem; background: #0b0b0b; border:1px solid rgba(0,255,204,0.08); min-width:190px; border-radius:8px; padding:.3rem; box-shadow: 0 14px 40px rgba(0,0,0,0.85); }
            .dropdown-item{ display:block; padding:.55rem .85rem; color:var(--neon-cyan); text-decoration:none; border-radius:6px; font-weight:700; }
            .dropdown-item:hover{ background: rgba(0,255,204,0.08); color:#000; }
            @media (max-width:768px) { :root{ --nav-height:120px; } .reserved-nav-space{height:var(--nav-height);} .fixed-profile{ display:none; } }
        </style>';

        $html .= '</head><body class="page-bg admin-page">';
        $html .= '<div class="reserved-nav-space"></div>';

        // Nav (fijo, menu dentro del container aligned left)
        $html .= '<nav x-data="{ open: false }" class="gamer-nav" aria-label="Primary">';
        $html .= '<div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">';
        $html .= '<div class="nav-inner flex items-center h-16">';
        $html .= '<div class="left-sections">';
        $html .= '<ul class="gamer-menu" role="menubar">';
        $html .= '<li role="none"><a role="menuitem" href="'.route('dashboard').'" class="gamer-link">INICIO</a></li>';
        $html .= '<li role="none"><a role="menuitem" href="'.route('admin.reservas').'" class="gamer-link">GESTIONAR RESERVAS</a></li>';
        $html .= '</ul>';
        $html .= '</div>'; // left-sections
        $html .= '</div></div></nav>';

        // Profile fixed (outside the container)
        $html .= '<div class="fixed-profile" x-data="{ profileOpen: false }" @click.outside="profileOpen = false">';
        if ($user) {
            $html .= '<div class="relative">';
            $html .= '<button @click="profileOpen = !profileOpen" class="gamer-btn-rect" aria-haspopup="true" :aria-expanded="profileOpen.toString()">';
            $html .= '<span class="perfil-name">'.htmlspecialchars($user->name).'</span>';
            $html .= '<svg class="chev" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>';
            $html .= '</button>';
            $html .= '<div x-show="profileOpen" x-transition class="dropdown-gamer" x-cloak>';
            $html .= '<a href="'.route('profile.edit').'" class="dropdown-item">Profile</a>';
            $html .= '<form method="POST" action="'.route('logout').'">';
            $html .= '<input type="hidden" name="_token" value="'.$csrf.'">';
            $html .= '<button type="submit" class="dropdown-item">Log Out</button>';
            $html .= '</form></div></div>';
        }
        $html .= '</div>'; // fixed-profile

        // Main container
        $html .= '<div class="container page-wrapper">';

        // Card header
        $html .= '<div class="card">';
        $html .= '<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:20px;">';
        $html .= '<div><h1 class="card-title" style="color:var(--neon-cyan);margin:0 0 6px 0">Panel de Administración</h1>';
        $html .= '<div class="card-sub small" style="color:#bfbfbf;">Gestiona las reservas — busca, filtra y actúa desde aquí.</div></div>';
        $html .= '<div class="header-actions">';
        $html .= '<a href="'.route('admin.reservas').'" class="gamer-btn-rect">Refrescar</a>';
        $html .= '</div></div>';

        // Filters & search
        $html .= '<form method="GET" action="'.route('admin.reservas').'" class="filters">';
        $html .= '<input type="search" name="q" placeholder="Buscar usuario o consola" value="'.htmlspecialchars($q).'" style="padding:10px;border-radius:8px;background:#111;border:1px solid rgba(0,255,204,0.06);color:#fff;min-width:260px;">';
        $html .= '<select name="status" style="padding:10px;border-radius:8px;background:#111;border:1px solid rgba(0,255,204,0.06);color:#fff;">';
        $html .= '<option value="all"'.($status==='all' ? ' selected':'').'>Todos los estados</option>';
        $html .= '<option value="pending"'.($status==='pending' ? ' selected':'').'>Pending</option>';
        $html .= '<option value="paid"'.($status==='paid' ? ' selected':'').'>Paid</option>';
        $html .= '<option value="canceled"'.($status==='canceled' ? ' selected':'').'>Canceled</option>';
        $html .= '</select>';
        $html .= '<select name="consola_id" style="padding:10px;border-radius:8px;background:#111;border:1px solid rgba(0,255,204,0.06);color:#fff;">';
        $html .= '<option value="">Todas las consolas</option>';
        foreach ($consolas as $c) {
            $sel = ($consolaId == $c->id) ? ' selected' : '';
            $html .= '<option value="'. $c->id .'"'. $sel .'>'.htmlspecialchars($c->nombre).'</option>';
        }
        $html .= '</select>';
        $html .= '<button class="neon-btn" type="submit" style="padding:10px 14px;">Buscar</button>';
        $html .= '<a href="'.route('admin.reservas').'" class="btn-ghost" style="padding:10px 14px;">Limpiar</a>';
        $html .= '</form>';

        // Stats
        $html .= '<div class="stats-grid">';
        $html .= '<div class="stat"><div class="small">Total ganancias (pagadas)</div><div style="font-weight:700;margin-top:8px;">$'.number_format($totalGanancias ?? 0,0,',','.').'</div></div>';
        $html .= '<div class="stat"><div class="small">Total pendientes</div><div style="font-weight:700;margin-top:8px;">$'.number_format($totalPendientes ?? 0,0,',','.').'</div></div>';
        $html .= '<div class="stat"><div class="small">Reservas pagadas</div><div style="font-weight:700;margin-top:8px;">'.$pagadas.'</div></div>';
        $html .= '<div class="stat"><div class="small">Reservas pendientes</div><div style="font-weight:700;margin-top:8px;">'.$pendientes.'</div></div>';
        $html .= '</div>';

        // Tabla de reservas
        $html .= '<div class="table-wrap"><div style="overflow-x:auto;margin-top:16px;">';
        $html .= '<table class="table" style="min-width:1000px;">';
        $html .= '<thead><tr><th>Usuario</th><th>Consola</th><th>Inicio</th><th>Fin</th><th>Precio</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>';

        foreach ($reservas as $r) {
            $userName = htmlspecialchars($r->user->name ?? '—');
            $userEmail = htmlspecialchars($r->user->email ?? '');
            $consola = htmlspecialchars($r->consola->nombre ?? '—');
            $start = optional($r->start_at)->format('Y-m-d g:i A');
            $end = optional($r->end_at)->format('Y-m-d g:i A');
            $precio = '$'.number_format($r->precio_total ?? 0, 0, ',', '.');
            $estado = htmlspecialchars(ucfirst($r->status ?? '—'));

            $html .= '<tr>';
            $html .= '<td style="min-width:200px;"><strong>'.$userName.'</strong><br><small class="small">'.$userEmail.'</small></td>';
            $html .= '<td style="min-width:240px;">'.$consola.'</td>';
            $html .= '<td>'.$start.'</td>';
            $html .= '<td>'.$end.'</td>';
            $html .= '<td>'.$precio.'</td>';
            $html .= '<td>'.$estado.'</td>';

            $html .= '<td style="min-width:220px;">';
            $actionDelete = route('admin.reservas.destroy', $r);
            $html .= '<form action="'.$actionDelete.'" method="POST" style="display:inline-block;">';
            $html .= '<input type="hidden" name="_token" value="'.$csrf.'">';
            $html .= '<input type="hidden" name="_method" value="DELETE">';
            $html .= '<button class="btn-ghost" type="submit" onclick="return confirm(\'¿Eliminar reserva? Esta acción no tiene vuelta atrás.\')">Eliminar</button>';
            $html .= '</form>';
            $html .= '</td>';

            $html .= '</tr>';
        }

        $html .= '</tbody></table></div></div>';

        // Paginación
        $html .= '<div class="pagination" aria-label="Paginación">';
        $last = $reservas->lastPage();
        $current = $reservas->currentPage();

        if ($reservas->onFirstPage() === false) {
            $html .= '<a class="page-link" href="'.htmlspecialchars($reservas->previousPageUrl()).'">&laquo; Prev</a>';
        }

        $start = max(1, $current - 2);
        $end = min($last, $current + 2);
        for ($i = $start; $i <= $end; $i++) {
            $aria = ($i === $current) ? ' aria-current="page"' : '';
            $html .= '<a class="page-link" href="'.htmlspecialchars($reservas->url($i)).'"'.$aria.'>'.$i.'</a>';
        }

        if ($reservas->hasMorePages()) {
            $html .= '<a class="page-link" href="'.htmlspecialchars($reservas->nextPageUrl()).'">Next &raquo;</a>';
        }
        $html .= '</div>'; // pagination

        $html .= '</div>'; // card

        // Consolas: sección para crear/editar/eliminar
        $html .= '<div class="consola-section">';
        $html .= '<h2 class="card-title" style="color:var(--neon-cyan); margin-top:18px;">Administrar Consolas</h2>';
        $html .= '<div class="card" style="margin-top:10px;">';
        $html .= '<div class="small">Agrega nuevas consolas, actualiza precio o elimina consolas (no se puede eliminar consola con reservas pagadas).</div>';

        // Form crear consola (simple) - ahora descripcion es textarea con altura fija
        $html .= '<form action="'.route('admin.consolas.store').'" method="POST" style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap;align-items:flex-start;">';
        $html .= '<input type="hidden" name="_token" value="'.$csrf.'">';
        $html .= '<input name="nombre" placeholder="Nombre consola" style="padding:10px;border-radius:8px;background:#071010;border:1px solid rgba(0,255,204,0.04);color:#cfeee6;min-width:220px;">';
        $html .= '<input name="precio_hora" placeholder="Precio / hora" style="padding:10px;border-radius:8px;background:#071010;border:1px solid rgba(0,255,204,0.04);color:#cfeee6;min-width:140px;">';
        $html .= '<textarea name="descripcion" placeholder="Descripción (opcional)" style="padding:10px;border-radius:8px;background:#071010;border:1px solid rgba(0,255,204,0.04);color:#cfeee6;min-width:300px;height:64px;max-height:120px;resize:vertical;overflow:auto;"></textarea>';
        $html .= '<button class="gamer-btn-rect" type="submit" style="padding:10px 14px;">Agregar consola</button>';
        $html .= '</form>';

        $html .= '<div class="consola-grid">';

        foreach ($consolas as $c) {
            $html .= '<div class="consola-card" style="background:linear-gradient(180deg,#0b0b0b,#111);border-radius:8px;border:1px solid rgba(0,255,204,0.03);">';
            // Update form (nombre, precio, descripcion) - textarea con altura fija
            $html .= '<form action="'.route('admin.consolas.update_price', $c).'" method="POST">';
            $html .= '<input type="hidden" name="_token" value="'.$csrf.'">';
            $html .= '<div style="font-weight:700;margin-bottom:8px;color:var(--neon-cyan);">'.htmlspecialchars($c->nombre).'</div>';

            $html .= '<div style="display:flex;gap:8px;align-items:center;margin-bottom:8px;">';
            $html .= '<input name="nombre" value="'.htmlspecialchars($c->nombre).'" placeholder="Nombre" style="padding:8px;border-radius:6px;background:#071010;border:1px solid rgba(0,255,204,0.04);color:#cfeee6;width:100%;">';
            $html .= '</div>';

            $html .= '<div style="display:flex;gap:8px;align-items:center;margin-bottom:8px;">';
            $html .= '<input name="precio_hora" value="'.htmlspecialchars($c->precio_hora).'" placeholder="Precio / hora" style="padding:8px;border-radius:6px;background:#071010;border:1px solid rgba(0,255,204,0.04);color:#cfeee6;width:140px;">';
            $html .= '<textarea name="descripcion" rows="2" placeholder="Descripción (visible en reservas)" style="padding:8px;border-radius:6px;background:#071010;border:1px solid rgba(0,255,204,0.04);color:#cfeee6;width:100%;height:64px;max-height:120px;resize:vertical;overflow:auto;">'.htmlspecialchars($c->descripcion ?? '').'</textarea>';
            $html .= '</div>';

            $html .= '<div class="consola-actions">';
            $html .= '<button class="btn-ghost" type="submit" style="padding:8px 10px;">Guardar</button>';
            $html .= '</form>';

            // Delete form
            $html .= '<form action="'.route('admin.consolas.destroy', $c).'" method="POST" style="display:inline;margin-left:auto;">';
            $html .= '<input type="hidden" name="_token" value="'.$csrf.'">';
            $html .= '<input type="hidden" name="_method" value="DELETE">';
            $html .= '<button class="btn-ghost" type="submit" onclick="return confirm(\'¿Eliminar esta consola? Si tiene reservas pagadas no podrá eliminarse.\')">Eliminar</button>';
            $html .= '</form>';

            $html .= '</div>'; // consola-actions
            $html .= '</div>'; // consola-card
        }

        $html .= '</div>'; // consola-grid
        $html .= '</div>'; // card
        $html .= '</div>'; // consola-section

        // Fin container
        $html .= '</div></body></html>';

        return response($html, 200)->header('Content-Type', 'text/html');
    }

    /**
     * Marca como pagada (admin)
     *
     * Nota: dejamos el método disponible pero NO hay ruta pública para que el admin lo marque
     * (según tu instrucción de que el admin no pueda marcar como pagada).
     */
    public function adminMarkPaid(Reserva $reserva)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        $reserva->update(['status' => 'paid']);
        return back()->with('success', 'Reserva marcada como pagada.');
    }

    /**
     * Actualiza precio / nombre / descripcion de consola (admin)
     * Ruta: POST admin/consolas/{consola}/update-price
     */
    public function adminUpdateConsolaPrice(Request $request, Consola $consola)
    {
        if (!Auth::user()->isAdmin()) abort(403);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio_hora' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:2000',
        ]);

        $consola->update([
            'nombre' => $request->nombre,
            'precio_hora' => $request->precio_hora,
            'descripcion' => $request->descripcion ?? null,
        ]);

        return back()->with('success', 'Consola actualizada.');
    }

    /**
     * Crear nueva consola desde admin
     * Ruta: POST admin/consolas
     */
    public function adminStoreConsola(Request $request)
    {
        if (!Auth::user()->isAdmin()) abort(403);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio_hora' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:2000',
        ]);

        Consola::create([
            'nombre' => $request->nombre,
            'precio_hora' => $request->precio_hora,
            'descripcion' => $request->descripcion ?? null,
        ]);

        return back()->with('success', 'Consola creada.');
    }

    /**
     * Eliminar consola (si no tiene reservas pagadas)
     * Ruta: DELETE admin/consolas/{consola}
     */
    public function adminDestroyConsola(Consola $consola)
    {
        if (!Auth::user()->isAdmin()) abort(403);

        $hasPaid = Reserva::where('consola_id', $consola->id)->where('status', 'paid')->exists();
        if ($hasPaid) {
            return back()->withErrors('No se puede eliminar una consola que tiene reservas pagadas asociadas.');
        }

        $consola->delete();
        return back()->with('success', 'Consola eliminada.');
    }
}
