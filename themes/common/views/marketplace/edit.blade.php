@extends('theme::layouts.app')

@section('title', __('marketplace.edit') . ' - ' . site_name())

@section('content')
    <div class="mb-6">
        <a href="{{ route('marketplace.show', $listing) }}" class="text-sm font-medium theme-link-primary hover:underline">{{ __('marketplace.back') }}</a>
    </div>
    <h1 class="page-title mb-2">{{ __('marketplace.edit') }}</h1>

    @if($errors->any())
        <ul class="mb-6 list-disc list-inside text-red-600 dark:text-red-400 text-sm rounded-2xl bg-red-50 dark:bg-red-900/10 p-4 border border-red-200/80 dark:border-red-800/50">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    @endif

    <div class="card max-w-xl">
        <form action="{{ route('marketplace.update', $listing) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label for="title" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('marketplace.title') }}</label>
                <input type="text" name="title" id="title" value="{{ old('title', $listing->title) }}" required class="form-input">
            </div>
            <div>
                <label for="category_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('marketplace.category') }}</label>
                <select name="category_id" id="category_id" class="form-input">
                    <option value="">{{ __('general.no_value') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string) old('category_id', $listing->category_id) === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('marketplace.description') }}</label>
                <livewire:cms-rich-editor-field name="description" :value="old('description', $listing->description)" :compact="true" />
                @error('description')<p class="text-red-600 dark:text-red-400 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="price_type" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('marketplace.price_type') }}</label>
                    <select name="price_type" id="price_type" class="form-input">
                        <option value="negotiable" {{ old('price_type', $listing->price_type) === 'negotiable' ? 'selected' : '' }}>{{ __('marketplace.price_negotiable') }}</option>
                        <option value="fixed" {{ old('price_type', $listing->price_type) === 'fixed' ? 'selected' : '' }}>{{ __('marketplace.price_fixed') }}</option>
                        <option value="free" {{ old('price_type', $listing->price_type) === 'free' ? 'selected' : '' }}>{{ __('marketplace.price_free') }}</option>
                    </select>
                </div>
                <div>
                    <label for="price_value" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('marketplace.price_value') }}</label>
                    <input type="number" name="price_value" id="price_value" value="{{ old('price_value', $listing->price_value) }}" min="0" step="0.01" class="form-input">
                </div>
            </div>
            <div>
                <label for="contact_info" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('marketplace.contact_info') }}</label>
                <input type="text" name="contact_info" id="contact_info" value="{{ old('contact_info', $listing->contact_info) }}" class="form-input">
            </div>
            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="btn-primary">{{ __('marketplace.save') }}</button>
                <a href="{{ route('marketplace.show', $listing) }}" class="inline-flex items-center justify-center px-4 py-3 rounded-xl border border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 font-medium transition-colors">{{ __('marketplace.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
