<nav id="sidebar" class="d-flex flex-column">
    <div class="sidebar-header d-flex align-items-center justify-content-center">
        <h4 class="mb-0 text-center fw-bold py-2">
            @if($isAdmin)
                <span class="text-danger">Admin</span> Panel
            @else
                RiskIntel
            @endif
        </h4>
    </div>

    <ul class="list-unstyled components flex-grow-1">
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
            <li class="{{ request()->routeIs('admin.countries.*') ? 'active' : '' }}">
                <a href="{{ route('admin.countries.index') }}"><i class="bi bi-globe me-2"></i>Kelola Negara</a>
            </li>
            <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}"><i class="bi bi-people me-2"></i>Kelola User</a>
            </li>
            <li class="{{ request()->routeIs('admin.news-cache.*') ? 'active' : '' }}">
                <a href="{{ route('admin.news-cache.index') }}"><i class="bi bi-database-fill me-2"></i>News Cache</a>
            </li>
            <li class="{{ request()->routeIs('admin.watchlists.*') ? 'active' : '' }}">
                <a href="{{ route('admin.watchlists.index') }}"><i class="bi bi-star-fill me-2"></i>Global Watchlist</a>
            </li>
            <li class="mt-5">
                <a href="{{ route('user.index') }}" class="text-info"><i class="bi bi-arrow-left-circle me-2"></i>Dasbor User</a>
            </li>
        @else
            <!-- User Menu (11 Items) -->
            <li class="{{ request()->routeIs('user.index') ? 'active' : '' }}">
                <a href="{{ route('user.index') }}"><i class="bi bi-house-door me-2"></i>Dashboard</a>
            </li>
            <li class="{{ request()->routeIs('user.country') ? 'active' : '' }}">
                <a href="{{ route('user.country') }}"><i class="bi bi-globe me-2"></i>Countries</a>
            </li>
            <li class="{{ request()->routeIs('user.weather') ? 'active' : '' }}">
                <a href="{{ route('user.weather') }}"><i class="bi bi-cloud-sun me-2"></i>Weather</a>
            </li>
            <li class="{{ request()->routeIs('user.currency') ? 'active' : '' }}">
                <a href="{{ route('user.currency') }}"><i class="bi bi-currency-exchange me-2"></i>Currency</a>
            </li>
            <li class="{{ request()->routeIs('user.news') ? 'active' : '' }}">
                <a href="{{ route('user.news') }}"><i class="bi bi-newspaper me-2"></i>News</a>
            </li>
            <li class="{{ request()->routeIs('user.ports') ? 'active' : '' }}">
                <a href="{{ route('user.ports') }}"><i class="bi bi-compass me-2"></i>Ports</a>
            </li>
            <li class="{{ request()->routeIs('user.risk') ? 'active' : '' }}">
                <a href="{{ route('user.risk') }}"><i class="bi bi-shield-check me-2"></i>Risk Analysis</a>
            </li>
            <li class="{{ request()->routeIs('user.compare') ? 'active' : '' }}">
                <a href="{{ route('user.compare') }}"><i class="bi bi-columns-gap me-2"></i>Comparison</a>
            </li>
            <li class="{{ request()->routeIs('user.watchlist') ? 'active' : '' }}">
                <a href="{{ route('user.watchlist') }}"><i class="bi bi-star me-2"></i>Watchlist</a>
            </li>
            <li class="{{ request()->routeIs('user.articles') ? 'active' : '' }}">
                <a href="{{ route('user.articles') }}"><i class="bi bi-journal-text me-2"></i>Articles</a>
            </li>
            <li class="{{ request()->routeIs('user.settings') ? 'active' : '' }}">
                <a href="{{ route('user.settings') }}"><i class="bi bi-gear me-2"></i>Settings</a>
            </li>
        @endif
    </ul>

    <div class="p-3 text-center" style="font-size: 0.85em; color: #64748b; border-top: 1px solid #e2e8f0;">
        v1.0.0 &copy; 2026
    </div>
</nav>
