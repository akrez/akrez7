<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts._head')

    @stack('styles')
</head>

<body dir="rtl">
    <div id="app">
        @include('layouts._navigation')

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>
