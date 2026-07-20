<nav class="navbar navbar-expand-lg navbar-light navbar-custom">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1 text-secondary">
            @yield('page_title', 'Dashboard')
        </span>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
            <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">
                <!-- Theme Toggle Switch -->
                <li class="nav-item me-3">
                    <button id="theme-toggle" class="btn btn-sm btn-light border border-light-subtle d-flex align-items-center justify-content-center p-0" style="width: 32px; height: 32px; border-radius: 50%;" title="Ganti Tema">
                        <i class="bi bi-moon-stars text-secondary" id="theme-icon"></i>
                    </button>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark fw-bold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i>
                        {{ auth()->user() ? auth()->user()->name : 'Guest User' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil</a></li>
                        @if(auth()->user() && auth()->user()->role === 'admin')
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-lock me-2"></i>Admin Panel</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
