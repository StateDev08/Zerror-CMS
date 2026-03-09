@extends('theme::layouts.app')

@section('title', __('nav.clan_documents') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{ __('nav.clan_documents') }}</h1>
    @if($categories->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">{{ __('clan.documents_none') }}</p>
    @else
        @foreach($categories as $cat)
            <section class="mb-6">
                <h2 class="text-xl font-semibold mb-3">{{ $cat->name }}</h2>
                @if($cat->documents->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('clan.no_documents') }}</p>
                @else
                    <ul class="space-y-2">
                        @foreach($cat->documents as $doc)
                            <li>
                                <a href="{{ route('clan-documents.show', $doc) }}" class="flex items-center gap-2 text-amber-600 dark:text-amber-400 hover:underline">
                                    {{ $doc->title }}
                                    @if($doc->file_path)<span class="text-xs text-gray-400">({{ __('clan.download') }})</span>@endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach
    @endif
@endsection
