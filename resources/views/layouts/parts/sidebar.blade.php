<nav id="sidebar" class="d-flex flex-column">
    <div class="sidebar-header d-flex align-items-center justify-content-between px-3 py-3 border-bottom">
        <h5 class="mb-0 fw-bold sidebar-logo-text text-truncate">
            @if($isAdmin)
                <span class="text-danger">Admin</span> Panel
            @else
                RiskIntel
            @endif
        </h5>
        <h5 class="mb-0 fw-bold sidebar-logo-icon d-none" style="font-size: 1.15rem;">
            @if($isAdmin)
                <span class="text-danger">A</span>P
            @else
                RI
            @endif
        </h5>
        <button id="sidebar-toggle" class="btn btn-sm btn-light border border-light-subtle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; border-radius: 50%; transition: all 0.2s;">
            <i class="bi bi-chevron-left text-muted" id="toggle-icon"></i>
        </button>
    </div>

    <ul class="list-unstyled components flex-grow-1">
        @if($isAdmin)
            <!-- Admin Menu -->
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i><span class="menu-text">Ringkasan Admin</span></a>
            </li>
            <li class="{{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                <a href="{{ route('admin.articles.index') }}"><i class="bi bi-journal-text me-2"></i><span class="menu-text">Kelola Artikel</span></a>
            </li>
            <li class="{{ request()->routeIs('admin.ports.*') ? 'active' : '' }}">
                <a href="{{ route('admin.ports.index') }}"><i class="bi bi-anchor me-2"></i><span class="menu-text">Kelola Pelabuhan</span></a>
            </li>
            <li class="{{ request()->routeIs('admin.countries.*') ? 'active' : '' }}">
                <a href="{{ route('admin.countries.index') }}"><i class="bi bi-globe me-2"></i><span class="menu-text">Kelola Negara</span></a>
            </li>
            <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}"><i class="bi bi-people me-2"></i><span class="menu-text">Kelola User</span></a>
            </li>
            <li class="{{ request()->routeIs('admin.news-cache.*') ? 'active' : '' }}">
                <a href="{{ route('admin.news-cache.index') }}"><i class="bi bi-database-fill me-2"></i><span class="menu-text">News Cache</span></a>
            </li>
            <li class="{{ request()->routeIs('admin.watchlists.*') ? 'active' : '' }}">
                <a href="{{ route('admin.watchlists.index') }}"><i class="bi bi-star-fill me-2"></i><span class="menu-text">Global Watchlist</span></a>
            </li>
            <li class="mt-5">
                <a href="{{ route('user.index') }}" class="text-info"><i class="bi bi-arrow-left-circle me-2"></i><span class="menu-text">Dasbor User</span></a>
            </li>
        @else
            <!-- User Menu (11 Items) -->
            <li class="{{ request()->routeIs('user.index') ? 'active' : '' }}">
                <a href="{{ route('user.index') }}"><i class="bi bi-house-door me-2"></i><span class="menu-text">Dashboard</span></a>
            </li>
            <li class="{{ request()->routeIs('user.shipments.*') ? 'active' : '' }}">
                <a href="{{ route('user.shipments.index') }}"><i class="bi bi-box-seam me-2"></i><span class="menu-text">Shipments</span></a>
            </li>
            <li class="{{ request()->routeIs('user.country') ? 'active' : '' }}">
                <a href="{{ route('user.country') }}"><i class="bi bi-globe me-2"></i><span class="menu-text">Countries</span></a>
            </li>
            <li class="{{ request()->routeIs('user.weather') ? 'active' : '' }}">
                <a href="{{ route('user.weather') }}"><i class="bi bi-cloud-sun me-2"></i><span class="menu-text">Weather</span></a>
            </li>
            <li class="{{ request()->routeIs('user.currency') ? 'active' : '' }}">
                <a href="{{ route('user.currency') }}"><i class="bi bi-currency-exchange me-2"></i><span class="menu-text">Currency</span></a>
            </li>
            <li class="{{ request()->routeIs('user.news') ? 'active' : '' }}">
                <a href="{{ route('user.news') }}"><i class="bi bi-newspaper me-2"></i><span class="menu-text">News</span></a>
            </li>
            <li class="{{ request()->routeIs('user.ports') ? 'active' : '' }}">
                <a href="{{ route('user.ports') }}"><i class="bi bi-compass me-2"></i><span class="menu-text">Ports</span></a>
            </li>
            <li class="{{ request()->routeIs('user.risk') ? 'active' : '' }}">
                <a href="{{ route('user.risk') }}"><i class="bi bi-shield-check me-2"></i><span class="menu-text">Risk Analysis</span></a>
            </li>
            <li class="{{ request()->routeIs('user.compare') ? 'active' : '' }}">
                <a href="{{ route('user.compare') }}"><i class="bi bi-columns-gap me-2"></i><span class="menu-text">Comparison</span></a>
            </li>
            <li class="{{ request()->routeIs('user.watchlist') ? 'active' : '' }}">
                <a href="{{ route('user.watchlist') }}"><i class="bi bi-star me-2"></i><span class="menu-text">Watchlist</span></a>
            </li>
            <li class="{{ request()->routeIs('user.articles') ? 'active' : '' }}">
                <a href="{{ route('user.articles') }}"><i class="bi bi-journal-text me-2"></i><span class="menu-text">Articles</span></a>
            </li>
            <li class="{{ request()->routeIs('user.settings') ? 'active' : '' }}">
                <a href="{{ route('user.settings') }}"><i class="bi bi-gear me-2"></i><span class="menu-text">Settings</span></a>
            </li>
        @endif
    </ul>
</nav>
