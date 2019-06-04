<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Costs') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini">
    <div id="app">
        <div class="wrapper">

            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
                    </li>
                </ul>

                <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        <li class="nav-item">
                            @if (Route::has('register'))
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            @endif
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                @endguest
            </nav>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <button type="button" class="close d-sm-none" data-widget="pushmenu">
                    <span aria-hidden="true">&times;</span>
                </button>

                <!-- Brand Logo -->
                <a href="{{ url('/') }}" class="brand-link">
                    @svg('svg/logo.svg', 'brand-image elevation-3')
                    <span class="brand-text font-weight-light">{{ config('app.name', 'Costs') }}</span>
                </a>

                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Sidebar user panel (optional) -->
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                            @if(Auth::user()->photo)
                                <img class="img-circle elevation-2" src="{{ Auth::user()->photo }}" alt="{{ Auth::user()->name }}">
                            @else
                                @svg('svg/user.svg', 'user-profile-image elevation-2')
                            @endif
                        </div>
                        <div class="info">
                            <router-link to="/profile" class="d-block">{{ Auth::user()->name }}</router-link>
                        </div>
                    </div>

                    <!-- Sidebar Menu -->
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                            <li class="nav-item">
                                <router-link to="/dashboard" class="nav-link">
                                    <i class="fas fa-tachometer-alt nav-icon text-blue"></i>
                                    <p>Dashboard</p>
                                </router-link>
                                <li class="nav-item">
                                    <router-link to="/transactions" class="nav-link">
                                        <i class="fas fa-exchange-alt nav-icon text-yellow"></i>
                                        <p>{{ __('Transactions') }}</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/accounts" class="nav-link">
                                        <i class="fas fa-coins nav-icon text-yellow"></i>
                                        <p>{{ __('Accounts') }}</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/places" class="nav-link">
                                        <i class="fas fa-map-marker-alt nav-icon text-green"></i>
                                        <p>{{ __('Places') }}</p>
                                    </router-link>
                                </li>
                            </li>
                            @can('viewAll', \App\User::class)
                                <li class="nav-item has-treeview">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-cogs text-purple"></i>
                                        <p>
                                            {{ __('Management') }}
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview ml-3">
                                        <li class="nav-item">
                                            <router-link to="/users" class="nav-link">
                                                <i class="fas fa-users nav-icon text-orange"></i>
                                                <p>{{ __('Users') }}</p>
                                            </router-link>
                                        </li>
                                        <li class="nav-item">
                                            <router-link to="/apiUsers" class="nav-link">
                                                <i class="fas fa-unlock nav-icon text-orange"></i>
                                                <p>{{ __('API Users') }}</p>
                                            </router-link>
                                        </li>
                                        <li class="nav-item">
                                            <router-link to="/accountTypes" class="nav-link">
                                                <i class="fas fa-money-check-alt nav-icon text-yellow"></i>
                                                <p>{{ __('Account Types') }}</p>
                                            </router-link>
                                        </li>
                                        <li class="nav-item">
                                            <router-link to="/transactionTypes" class="nav-link">
                                                <i class="fas fa-exchange-alt nav-icon text-green"></i>
                                                <p>{{ __('Transaction Types') }}</p>
                                            </router-link>
                                        </li>
                                        <li class="nav-item">
                                            <router-link to="/transactionCategories" class="nav-link">
                                                <i class="fas fa-pizza-slice nav-icon text-yellow"></i>
                                                <p>{{ __('Transaction Categories') }}</p>
                                            </router-link>
                                        </li>
                                        <li class="nav-item">
                                            <router-link to="/transactionCategoryTypes" class="nav-link">
                                                <i class="fas fa-search-dollar nav-icon text-blue"></i>
                                                <p>{{ __('Transaction Category Types') }}</p>
                                            </router-link>
                                        </li>
                                    </ul>
                                </li>
                            @endcan
                            <li class="nav-item">
                                <router-link to="/profile" class="nav-link">
                                    <i class="fas fa-user nav-icon text-green"></i>
                                    <p>{{ __('Profile') }}</p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('logout') }}" class="nav-link"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-power-off nav-icon text-red"></i>
                                    <p>{{ __('Logout') }}</p>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <!-- /.sidebar-menu -->
                </div>
                <!-- /.sidebar -->
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">

                <!-- Main content -->
                <div class="content">
                    <div class="container-fluid">
                        <router-view></router-view>
                        <vue-progress-bar></vue-progress-bar>
                    </div><!-- /.container-fluid -->
                </div>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

            <!-- Main Footer -->
            <footer class="main-footer">
                <!-- To the right -->
                <div class="float-right d-none d-sm-inline">
                    Big brother is watching you
                </div>
                <!-- Default to the left -->
                <strong>Copyright &copy; {{ date('Y') }} <a href="https://github.com/timoffmax">Timofey Maksymenko</a>.</strong> All rights reserved.
            </footer>
        </div>
        <!-- ./wrapper -->
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    @auth
        {{-- Translate ACL rules to Frontend --}}
        <script>
            window.user = @json(auth()->user());
        </script>
    @endauth
</body>
</html>
