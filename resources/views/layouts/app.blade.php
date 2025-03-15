<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts._head')

    @stack('styles')
</head>

<body dir="rtl">
    @include('layouts._navigation')
    <div class="container mt-3">
        @hasSection('header')
            <h1 class="fs-2 my-4">
                @yield('header')
                @hasSection('subheader')
                    <small class="text-muted">@yield('subheader')</small>
                @endif
            </h1>
        @endif
        @yield('content')
    </div>
    <script>
        @if (session('swal-success'))
            Swal.fire(
                {{ Illuminate\Support\Js::from([
                    'text' => session('swal-success'),
                    'icon' => 'success',
                    'timer' => 3000,
                    'showCloseButton' => true,
                    'showConfirmButton' => false,
                    'timerProgressBar' => true,
                ]) }}
            );
        @endif
        @if (session('swal-error'))
            Swal.fire(
                {{ Illuminate\Support\Js::from([
                    'text' => session('swal-error'),
                    'icon' => 'error',
                    'timer' => 3000,
                    'showCloseButton' => true,
                    'showConfirmButton' => false,
                    'timerProgressBar' => true,
                    'toast' => true,
                    'position' => 'bottom',
                ]) }}
            );
        @endif
    </script>
    @yield('POS_END')
</body>

</html>
