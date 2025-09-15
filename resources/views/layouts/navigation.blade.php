<nav x-data="{ open: false }" class="gamer-nav" aria-label="Primary">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="nav-inner flex items-center justify-between h-16">
            <!-- LEFT: Secciones -->
            <div class="flex items-center gap-6">
                <ul class="gamer-menu" role="menubar">
                    <li role="none"><a role="menuitem" href="{{ route('dashboard') }}" class="gamer-link">Inicio</a></li>

                    @auth
                        @if(! Auth::user()->isAdmin())
                            <li role="none"><a role="menuitem" href="{{ route('quienes-somos') }}" class="gamer-link">Quiénes Somos</a></li>
                            <li role="none"><a role="menuitem" href="{{ route('contacto') }}" class="gamer-link">Contáctanos</a></li>
                            <li role="none"><a role="menuitem" href="{{ route('reserva.index') }}" class="gamer-link">Reserva</a></li>
                        @else
                            {{-- admin únicamente --}}
                            <li role="none"><a role="menuitem" href="{{ route('admin.reservas') }}" class="gamer-link text-pink-400">Gestionar Reservas</a></li>
                        @endif
                    @endauth
                </ul>
            </div>

            <!-- RIGHT: Perfil -->
            <div class="profile-area flex items-center gap-3">
                @auth
                    <div class="relative" x-data="{ profileOpen: false }" @click.outside="profileOpen = false">
                        <button @click="profileOpen = !profileOpen" class="gamer-btn-rect" aria-haspopup="true" :aria-expanded="profileOpen.toString()">
                            <span class="perfil-name">{{ Auth::user()->name }}</span>
                            <svg class="chev" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path fill="currentColor" fill-rule="evenodd"
                                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <div x-show="profileOpen" x-transition class="dropdown-gamer" x-cloak>
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">Profile</a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Log Out</button>
                            </form>
                        </div>
                    </div>
                @endauth

                <!-- Mobile hamburger -->
                <div class="sm:hidden">
                    <button @click="open = !open" class="hamburger-btn" :aria-expanded="open.toString()" aria-controls="mobile-menu" aria-label="Abrir menú">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" x-show="open" x-transition class="sm:hidden" x-cloak>
        <div class="mobile-inner px-4 pb-4 pt-2">
            <ul class="gamer-menu-mobile flex flex-col gap-2">
                <li><a href="{{ route('dashboard') }}" class="gamer-link-mobile">Inicio</a></li>

                @auth
                    @if(! Auth::user()->isAdmin())
                        <li><a href="{{ route('quienes-somos') }}" class="gamer-link-mobile">Quiénes Somos</a></li>
                        <li><a href="{{ route('contacto') }}" class="gamer-link-mobile">Contáctanos</a></li>
                        <li><a href="{{ route('reserva.index') }}" class="gamer-link-mobile">Reserva</a></li>
                    @else
                        <li><a href="{{ route('admin.reservas') }}" class="gamer-link-mobile text-pink-400">Gestionar Reservas</a></li>
                    @endif
                @endauth
            </ul>

            @auth
                <div class="mobile-profile mt-4 border-t border-[#00ffcc33] pt-3">
                    <div class="text-sm text-[#00ffcc] font-semibold">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-gray-400 mb-2">{{ Auth::user()->email }}</div>

                    <a href="{{ route('profile.edit') }}" class="dropdown-item block mb-2">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item block">Log Out</button>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</nav>
