<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('install.title') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-2xl font-bold text-slate-800 mb-2">{{ __('install.title') }}</h1>
        <p class="text-slate-500 text-sm mb-6">{{ __('install.step_of', ['step' => $step ?? 1]) }}</p>
        @if(session('error'))
            <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 text-sm">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <ul class="mb-4 list-disc list-inside text-red-600 text-sm">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        @endif
        @yield('content')
    </div>
</body>
</html>
