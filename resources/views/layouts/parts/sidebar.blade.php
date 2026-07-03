<nav id="sidebar">
    <div class="sidebar-header d-flex align-items-center justify-content-center">
        <h4 class="mb-0 text-center fw-bold text-white py-2">
            @if($isAdmin)
                <span class="text-danger">Admin</span> Panel
            @else
                RiskIntel
            @endif
        </h4>
    </div>

    <ul class="list-unstyled components">
        @if($isAdmin)
            <!-- Admin Menu -->
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Ringkasan Admin</a>
            </li>
            <li class="{{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                <a href="{{ route('admin.articles.index') }}"><i class="bi bi-journal-text me-2"></i>Kelola Artikel</a>
            </li>
            <li class="{{ request()->routeIs('admin.ports.*') ? 'active' : '' }}">
                <a href="{{ route('admin.ports.index') }}"><i class="bi bi-anchor me-2"></i>Kelola Pelabuhan</a>
            </li>
            <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}"><i class="bi bi-people me-2"></i>Kelola User</a>
            </li>
            <li class="mt-5">
                <a href="{{ route('user.index') }}" class="text-info"><i class="bi bi-arrow-left-circle me-2"></i>Dasbor User</a>
            </li>
        @else
            <!-- User Menu -->
            <li class="{{ request()->routeIs('user.index') ? 'active' : '' }}">
                <a href="{{ route('user.index') }}"><i class="bi bi-house-door me-2"></i>Dasbor Utama</a>
            </li>
            <li class="{{ request()->routeIs('user.country') ? 'active' : '' }}">
                <a href="{{ route('user.country') }}"><i class="bi bi-globe me-2"></i>Dasbor Negara</a>
            </li>
            <li class="{{ request()->routeIs('user.watchlist') ? 'active' : '' }}">
                <a href="{{ route('user.watchlist') }}"><i class="bi bi-star me-2"></i>Daftar Favorit</a>
            </li>
            <li class="{{ request()->routeIs('user.compare') ? 'active' : '' }}">
                <a href="{{ route('user.compare') }}"><i class="bi bi-columns-gap me-2"></i>Bandingkan Negara</a>
            </li>
            <hr class="mx-3 my-2" style="border-top: 1px solid #475569;">
            <!-- Dummy menus matching requirements for staging -->
            <li>
                <a href="#weather"><i class="bi bi-cloud-sun me-2"></i>Peta Cuaca</a>
            </li>
            <li>
                <a href="#currency"><i class="bi bi-currency-exchange me-2"></i>Dasbor Valas</a>
            </li>
            <li>
                <a href="#news"><i class="bi bi-newspaper me-2"></i>Intel Berita</a>
            </li>
            <li>
                <a href="#ports"><i class="bi bi-compass me-2"></i>Peta Pelabuhan</a>
            </li>
        @endif
    </ul>

    <div class="p-3 mt-auto text-center" style="font-size: 0.85em; color: #64748b;">
        v1.0.0 &copy; 2026
    </div>
</nav>
