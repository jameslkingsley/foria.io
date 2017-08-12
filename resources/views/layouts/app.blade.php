<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Foria') }}</title>

        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">

        <script src="https://js.stripe.com/v3/"></script>
        <script src="https://static.opentok.com/v2/js/opentok.min.js"></script>
    </head>

    <body>
        <div id="app" v-cloak>
            @include('layouts.nav')

            <section class="section">
                <div class="container">
                    @yield('content')
                </div>
            </section>
        </div>

        <script src="{{ mix('js/app.js') }}"></script>
        @yield('scripts')
    </body>
</html>
