<nav class="navbar sidebar1 align-items-start sidebar sidebar-dark accordion bg-gradient-primary p-0 navbar-dark" style="background: var(--bs-primary-text-emphasis);">
    <div class="container-fluid d-flex flex-column p-0">
        <a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="#">
            <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-grip-lines-vertical"></i></div>
            <div class="sidebar-brand-text mx-3"><span>CyberScanosis</span></div>
        </a>
        <hr class="sidebar-divider my-0">
        <ul class="navbar-nav text-light" id="accordionSidebar">
            @if(Auth::check())
                <!-- Items to display if user is logged in -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('files') ? 'active' : '' }}" href="{{ route('files') }}">
                        <i class="far fa-folder-open"></i><span>Files</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('analysis*') ? 'active' : '' }}" href="#analysisSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="far fa-hdd"></i><span>Analysis</span>
                    </a>
                    <ul class="collapse list-unstyled show" id="analysisSubmenu">

                    <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('analysis.tasks.create') ? 'active' : '' }}" href="{{ route('analysis.tasks.create') }}">Task Creation</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('analysis.tasks.queue') ? 'active' : '' }}" href="{{ route('analysis.tasks.queue') }}">Task Queue</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('analysis.tasks.all') ? 'active' : '' }}" href="{{ route('analysis.tasks.all') }}">Tasks Completed</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('analysis.detections') ? 'active' : '' }}" href="{{ route('analysis.detections') }}">Malware Detections</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('analysis.logs') ? 'active' : '' }}" href="{{ route('analysis.logs') }}">Logs</a>
                        </li>
                    </ul>
                </li>


{{--                <li class="nav-item">--}}
{{--                    <a class="nav-link {{ request()->routeIs('upload') ? 'active' : '' }}" href="{{ route('upload') }}">--}}
{{--                        <i class="far fa-paper-plane"></i><span>Upload</span>--}}
{{--                    </a>--}}
{{--                </li>--}}





            @else
                <!-- Items to display if user is a guest -->

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
                        <i class="fas fa-user-circle"></i><span>Register</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                        <i class="fas fa-user-circle"></i><span>Login</span>
                    </a>
                </li>
            @endif
        </ul>
        <div class="text-center d-none d-md-inline"></div>
    </div>
</nav>
