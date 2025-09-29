@extends('layouts.app')

@section('content')
<div class="admin-page">
    <div class="container p-6">
        <div class="admin-card dashboard-card p-6 rounded-md shadow-sm bg-[#0f0f0f] text-white">
            <h1 class="text-3xl font-bold mb-2">Panel de Administración</h1>
            <p class="mb-4 text-sm text-gray-300">Bienvenido de nuevo, Admin. Aquí puedes gestionar tus reservas y usuarios.</p>

            <div class="space-x-3">
                <a href="{{ route('admin.reservas') }}" class="gamer-btn-rect inline-block">Gestionar Reservas</a>
                <a href="{{ route('dashboard') }}" class="gamer-btn-rect inline-block">Ir al Inicio</a>
                
            </div>
        </div>
    </div>
</div>
@endsection
