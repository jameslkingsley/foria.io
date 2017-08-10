<nav class="navbar">
    <div class="container">
        <div class="navbar-brand">
            <a class="navbar-item" href="{{ url('/') }}">
                Foria
            </a>

            <div class="navbar-burger burger" data-target="navMenu">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <div id="navMenu" class="navbar-menu">
            <div class="navbar-start">
                <a class="navbar-item">
                    Home
                </a>
            </div>

            <div class="navbar-end">
                @guest
                    <a class="navbar-item" href="{{ route('login') }}">
                        Login
                    </a>

                    <a class="navbar-item" href="{{ route('register') }}">
                        Register
                    </a>
                @else
                    <b-dropdown position="is-bottom-left">
                        <a class="navbar-item" slot="trigger">
                            <span>{{ auth()->user()->name }}</span>
                        </a>

                        <b-dropdown-item has-link>
                            <a href="{{ url('/watch/'.auth()->user()->name) }}">Profile</a>
                        </b-dropdown-item>

                        <b-dropdown-item has-link>
                            <a>Settings</a>
                        </b-dropdown-item>

                        <b-dropdown-item has-link>
                            <a href="{{ route('logout') }}">Logout</a>
                        </b-dropdown-item>
                    </b-dropdown>
                @endguest
            </div>
        </div>
    </div>
</nav>
