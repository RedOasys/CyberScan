<nav class="navbar headbar navbar-expand bg-white shadow mb-4 topbar static-top navbar-light" style="background: var(--bs-info);">
    <div class="container-fluid"><button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button>

        <ul class="navbar-nav flex-nowrap ms-auto">
            <li class="nav-item dropdown d-sm-none no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><i class="fas fa-search"></i></a>
                <div class="dropdown-menu dropdown-menu-end p-3 animated--grow-in" aria-labelledby="searchDropdown">

                </div>
            </li>
            <li class="nav-item dropdown no-arrow mx-1">

            <li class="nav-item dropdown no-arrow">
                <div class="nav-item dropdown no-arrow">
                    <a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#">
            <span class="d-none d-lg-inline me-2 text-gray-600 small">
                {{ Auth::check() ? Auth::user()->name : 'Guest' }}
            </span>
                    </a>
                    @if(Auth::check())
                        <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in">
                            <!-- Logout Option -->
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Logout
                            </a>

                            <!-- Logout Form -->
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    @endif
                </div>
            </li>
        </ul>
    </div>
</nav>
