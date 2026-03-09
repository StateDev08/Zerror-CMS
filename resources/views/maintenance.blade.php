<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('maintenance.title', ['name' => config('clan.name')]) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">
            {{ __('maintenance.heading', ['name' => config('clan.name')]) }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
            {{ $message ?? __('maintenance.message') }}
        </p>
        <a href="{{ url('/admin/login') }}" class="inline-block px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded font-medium">
            {{ __('maintenance.admin_login') }}
        </a>
    </div>
</body>
</html>
