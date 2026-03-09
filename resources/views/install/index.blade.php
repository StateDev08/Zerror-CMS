@extends('install.layout')

@section('content')
    @if($step === 1)
        <div class="space-y-4">
            <h2 class="font-semibold text-slate-800">{{ __('install.requirements') }}</h2>
            @if($requirements['ok'])
                <p class="text-green-600 text-sm">{{ __('install.requirements_ok') }}</p>
                <ul class="text-sm text-slate-600 space-y-1">
                    <li>✓ {{ __('install.requirements_php', ['version' => PHP_VERSION]) }}</li>
                    <li>✓ {{ __('install.requirements_extensions') }}</li>
                    <li>✓ {{ __('install.requirements_writable') }}</li>
                </ul>
                <a href="{{ route('install.index', ['step' => 2]) }}" class="inline-block mt-4 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium">{{ __('install.next_database') }}</a>
            @else
                <ul class="text-sm text-red-600 space-y-1">
                    @foreach($requirements['errors'] as $err)
                        <li>✗ {{ $err }}</li>
                    @endforeach
                </ul>
                <p class="text-slate-600 text-sm">{{ __('install.fix_errors') }}</p>
            @endif
        </div>

    @elseif($step === 2)
        <form action="{{ route('install.database') }}" method="POST" class="space-y-4">
            @csrf
            <h2 class="font-semibold text-slate-800">{{ __('install.database') }}</h2>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.db_host') }}</label>
                <input type="text" name="db_host" value="{{ old('db_host', '127.0.0.1') }}" class="w-full rounded border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.db_port') }}</label>
                <input type="text" name="db_port" value="{{ old('db_port', '3306') }}" class="w-full rounded border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.db_database') }}</label>
                <input type="text" name="db_database" value="{{ old('db_database', 'zerrocms') }}" class="w-full rounded border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.db_username') }}</label>
                <input type="text" name="db_username" value="{{ old('db_username', 'root') }}" class="w-full rounded border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.db_password') }}</label>
                <input type="password" name="db_password" value="{{ old('db_password') }}" class="w-full rounded border border-slate-300 px-3 py-2">
            </div>
            <p class="text-slate-500 text-xs">{{ __('install.db_hint') }}</p>
            <button type="submit" class="w-full mt-4 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium">{{ __('install.test_save') }}</button>
        </form>

    @elseif($step === 3)
        <div class="space-y-4">
            <h2 class="font-semibold text-slate-800">{{ __('install.migrate') }}</h2>
            <p class="text-slate-600 text-sm">{{ __('install.migrate_hint') }}</p>
            <a href="{{ route('install.migrate') }}" class="inline-block w-full text-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium">{{ __('install.run_migrate') }}</a>
        </div>

    @elseif($step === 4)
        <form action="{{ route('install.finish') }}" method="POST" class="space-y-4">
            @csrf
            <h2 class="font-semibold text-slate-800">{{ __('install.admin_user') }}</h2>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.admin_name') }}</label>
                <input type="text" name="admin_name" value="{{ old('admin_name', 'Admin') }}" class="w-full rounded border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.admin_email') }}</label>
                <input type="email" name="admin_email" value="{{ old('admin_email') }}" class="w-full rounded border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.admin_password') }}</label>
                <input type="password" name="admin_password" class="w-full rounded border border-slate-300 px-3 py-2" required minlength="8">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.admin_password_confirm') }}</label>
                <input type="password" name="admin_password_confirmation" class="w-full rounded border border-slate-300 px-3 py-2" required>
            </div>
            <p class="text-slate-500 text-xs">{{ __('install.admin_hint') }}</p>
            <button type="submit" class="w-full mt-4 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">{{ __('install.finish') }}</button>
        </form>

    @else
        <p class="text-slate-600">{{ __('install.invalid_step') }}</p>
        <a href="{{ route('install.index', ['step' => 1]) }}" class="text-amber-600 hover:underline">{{ __('install.back') }}</a>
    @endif
@endsection
