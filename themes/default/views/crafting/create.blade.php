@extends('theme::layouts.app')

@section('title', __('crafting.create_title') . ' - ' . config('clan.name'))

@section('content')
    <div class="mb-6">
        <a href="{{ route('crafting.index') }}" class="text-sm font-medium theme-link-primary hover:underline">{{ __('crafting.back_to_overview') }}</a>
    </div>
    <h1 class="page-title mb-2">{{ __('crafting.create_title') }}</h1>
    <p class="text-neutral-600 dark:text-neutral-400 mb-8">{{ __('crafting.create_intro') }}</p>

    @if(session('success'))
        <div class="alert-success mb-6">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <ul class="mb-6 list-disc list-inside text-red-600 dark:text-red-400 text-sm rounded-2xl bg-red-50 dark:bg-red-900/10 p-4 border border-red-200/80 dark:border-red-800/50">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    @endif

    <div class="card max-w-xl">
        <form action="{{ route('crafting.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="craftable_item_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('crafting.select_item') }}</label>
                <select name="craftable_item_id" id="craftable_item_id" class="form-input">
                    <option value="">{{ __('crafting.category_other') }} – {{ __('crafting.custom_request') }}</option>
                    @foreach($items->flatten() as $item)
                        <option value="{{ $item->id }}" {{ old('craftable_item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }} ({{ $item->category }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="custom_request" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('crafting.custom_request') }}</label>
                <textarea name="custom_request" id="custom_request" rows="4" class="form-input resize-y min-h-[100px]">{{ old('custom_request') }}</textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="max_price" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('crafting.max_price') }}</label>
                    <input type="number" name="max_price" id="max_price" value="{{ old('max_price') }}" min="0" step="0.01" class="form-input" placeholder="{{ __('general.no_value') }}">
                </div>
                <div>
                    <label for="desired_date" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('crafting.desired_date') }}</label>
                    <input type="date" name="desired_date" id="desired_date" value="{{ old('desired_date') }}" class="form-input">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="priority" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('crafting.priority') }}</label>
                    <select name="priority" id="priority" class="form-input">
                        @foreach(\App\Models\ItemRequest::priorityLabels() as $value => $label)
                            <option value="{{ $value }}" {{ old('priority', 'normal') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="quantity" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('crafting.quantity') }}</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" class="form-input">
                </div>
            </div>
            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="btn-primary">{{ __('crafting.submit') }}</button>
                <a href="{{ route('crafting.index') }}" class="inline-flex items-center justify-center px-4 py-3 rounded-xl border border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 font-medium transition-colors">{{ __('crafting.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
