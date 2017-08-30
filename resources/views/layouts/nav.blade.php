<nav class="navbar">
    <div class="container">
        <div class="navbar-brand">
            <a class="navbar-item" href="{{ url('/') }}">
                <img src="{{ url('/images/logo.png') }}">
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
                    <b-dropdown position="is-bottom-left" id="token-checkout">
                        <a class="navbar-item" slot="trigger">
                            <i class="material-icons m-r-2">local_play</i>
                            <span>@{{ user.tokens }} Tokens</span>
                        </a>

                        <b-dropdown-item custom>
                            <f-token-checkout :user="{{ auth()->user() }}"></f-token-checkout>
                        </b-dropdown-item>
                    </b-dropdown>

                    <b-dropdown position="is-bottom-left">
                        <a class="navbar-item" slot="trigger">
                            <span>{{ auth()->user()->name }}</span>
                            <i class="material-icons">keyboard_arrow_down</i>
                        </a>

                        <b-dropdown-item has-link>
                            <a href="{{ url('/profile/'.auth()->user()->name) }}">Profile</a>
                        </b-dropdown-item>

                        <b-dropdown-item has-link>
                            <a href="{{ url('/settings') }}">Settings</a>
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
