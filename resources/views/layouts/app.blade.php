<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Drivora</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon_io/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('favicon_io/site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('favicon_io/favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/toast.css') }}">
    <script src="{{ asset('js/toast.js') }}" defer></script>
</head>

@stack('styles')
@stack('scripts')

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 flex flex-col">

        @include('layouts.navigation')

        <div class="pt-16 flex flex-col flex-grow">

            @isset($header)
                <header class="bg-gradient-to-r from-blue-600 via-blue-400 to-gray-100">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                        <div>
                            {{ $header }}
                            <p class="text-blue-200 text-xs mt-0.5">
                                {{ now()->translatedFormat('l, d F Y') }}
                            </p>
                        </div>
                    </div>
                </header>
            @endisset

            <main class="flex-grow">
                {{-- Toast triggers --}}
                @if (session('success'))
                    <div hidden data-toast="success" data-toast-message="{{ session('success') }}">
                    </div>
                @endif

                @if (session('error'))
                    <div hidden data-toast="error" data-toast-message="{{ session('error') }}">
                    </div>
                @endif
                {{ $slot }}
            </main>

            @include('layouts.footer')

        </div>
    </div>
</body>


</html>
