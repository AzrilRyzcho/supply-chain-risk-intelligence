<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Global Supply Chain Risk Intelligence Platform')</title>
    
    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Leaflet Map CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Core Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    @stack('styles')
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script>
        (function() {
            const collapsed = localStorage.getItem('sidebar_collapsed') === 'true';
            if (collapsed) {
                document.documentElement.classList.add('sidebar-init-collapsed');
            }
        })();
    </script>
</head>
<body>
    <script>
        if (localStorage.getItem('dark_theme') === 'true') {
            document.body.classList.add('dark-theme');
        }
    </script>

    <div class="wrapper">
        <!-- Sidebar -->
        @include('layouts.parts.sidebar', ['isAdmin' => false])

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            @include('layouts.parts.navbar')

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show card-custom" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show card-custom" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Main Content Area -->
            <main>
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Leaflet Map JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <!-- Sidebar Toggle JS -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const sidebar = document.getElementById("sidebar");
            const toggleBtn = document.getElementById("sidebar-toggle");
            const toggleIcon = document.getElementById("toggle-icon");

            // Apply active class state on load
            const isCollapsed = localStorage.getItem("sidebar_collapsed") === "true";
            if (isCollapsed) {
                sidebar.classList.add("collapsed");
                if (toggleIcon) {
                    toggleIcon.classList.remove("bi-chevron-left");
                    toggleIcon.classList.add("bi-chevron-right");
                }
            } else {
                sidebar.classList.remove("collapsed");
                if (toggleIcon) {
                    toggleIcon.classList.remove("bi-chevron-right");
                    toggleIcon.classList.add("bi-chevron-left");
                }
            }

            // Remove the pre-rendering class once fully loaded
            document.documentElement.classList.remove("sidebar-init-collapsed");

            if (toggleBtn) {
                toggleBtn.addEventListener("click", function () {
                    const collapsed = sidebar.classList.toggle("collapsed");
                    localStorage.setItem("sidebar_collapsed", collapsed);

                    if (toggleIcon) {
                        if (collapsed) {
                            toggleIcon.classList.remove("bi-chevron-left");
                            toggleIcon.classList.add("bi-chevron-right");
                        } else {
                            toggleIcon.classList.remove("bi-chevron-right");
                            toggleIcon.classList.add("bi-chevron-left");
                        }
                    }

                    // Trigger map resize if Leaflet is present to prevent rendering bugs
                    if (typeof map !== 'undefined' && map && typeof map.invalidateSize === 'function') {
                        setTimeout(() => {
                            map.invalidateSize();
                        }, 300);
                    }
                });
            }

            // --- Theme Toggle Logic ---
            const themeToggleBtn = document.getElementById("theme-toggle");
            const themeIcon = document.getElementById("theme-icon");

            function updateThemeUI(dark) {
                if (dark) {
                    document.body.classList.add("dark-theme");
                    if (themeIcon) {
                        themeIcon.className = "bi bi-sun text-warning";
                    }
                } else {
                    document.body.classList.remove("dark-theme");
                    if (themeIcon) {
                        themeIcon.className = "bi bi-moon-stars text-secondary";
                    }
                }
            }

            // Sync state on DOM load
            const isDark = localStorage.getItem("dark_theme") === "true";
            updateThemeUI(isDark);

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener("click", function () {
                    const currentDark = document.body.classList.contains("dark-theme");
                    const newDark = !currentDark;
                    localStorage.setItem("dark_theme", newDark);
                    updateThemeUI(newDark);
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
