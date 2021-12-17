<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Scheduler</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script
        src="https://code.jquery.com/jquery-3.6.0.js"
        integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
        crossorigin="anonymous">
    </script>
    @yield('scripts')

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            @php
                                $arrayPath = explode('/', Request::path());
                            @endphp
                            @if (($arrayPath[0] === '') || ($arrayPath[0] === 'contracts') )
                                <li class="nav-item">
                                    <a href="{{ route('contracts.index') }}" class="nav-link text-danger">
                                        Contratti
                                    </a>
                                </li>
                                @if (Auth::user()['role'] == 'admin')
                                <li class="nav-item" id="clients">
                                    <a href="{{ route('clients.index') }}" class="nav-link">
                                        Aziende Clienti
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('cdcs.index') }}" class="nav-link">
                                        Centri di costo
                                    </a>
                                </li>
                                @endif
                                <li class="nav-item" id="tickets">
                                    <a href="{{ route('tickets.index') }}" class="nav-link">
                                        Ticket
                                    </a>
                                </li>
                            @elseif ($arrayPath[0] === 'clients')
                                <li class="nav-item">
                                    <a href="{{ route('contracts.index') }}" class="nav-link">
                                        Contratti
                                    </a>
                                </li>
                                @if (Auth::user()['role'] == 'admin')
                                <li class="nav-item">
                                    <a href="{{ route('clients.index') }}" class="nav-link text-danger">
                                        Aziende Clienti
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('cdcs.index') }}" class="nav-link">
                                        Centri di costo
                                    </a>
                                </li>
                                @endif
                                <li class="nav-item">
                                    <a href="{{ route('tickets.index') }}" class="nav-link">
                                        Ticket
                                    </a>
                                </li>
                            @elseif ($arrayPath[0] === 'tickets')
                                <li class="nav-item">
                                    <a href="{{ route('contracts.index') }}" class="nav-link">
                                        Contratti
                                    </a>
                                </li>
                                @if (Auth::user()['role'] == 'admin')
                                <li class="nav-item">
                                    <a href="{{ route('clients.index') }}" class="nav-link">
                                        Aziende Clienti
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('cdcs.index') }}" class="nav-link">
                                        Centri di costo
                                    </a>
                                </li>
                                @endif
                                <li class="nav-item">
                                    <a href="{{ route('tickets.index') }}" class="nav-link text-danger">
                                        Ticket
                                    </a>
                                </li>
                            @elseif ($arrayPath[0] === 'cdcs')
                                <li class="nav-item">
                                    <a href="{{ route('contracts.index') }}" class="nav-link">
                                        Contratti
                                    </a>
                                </li>
                                @if (Auth::user()['role'] == 'admin')
                                <li class="nav-item">
                                    <a href="{{ route('clients.index') }}" class="nav-link">
                                        Aziende Clienti
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('cdcs.index') }}" class="nav-link text-danger">
                                        Centri di costo
                                    </a>
                                </li>
                                @endif
                                <li class="nav-item">
                                    <a href="{{ route('tickets.index') }}" class="nav-link">
                                        Ticket
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item dropdown" id="user">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                @yield('headers')

                @include('messageBox')

                @yield('filters')
                
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
