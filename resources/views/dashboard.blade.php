<x-app-layout>
    <main class="page-bg">
        <div class="py-12 page-wrapper">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 content-area">
                <div class="dashboard-card">
                    <div class="p-6">
                        <h1 class="card-title">¡Has iniciado sesión!</h1>
                        <p class="card-sub">
                            Bienvenido de nuevo, {{ Auth::user()->name }} — administra tus reservas o tu perfil desde la barra superior.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
