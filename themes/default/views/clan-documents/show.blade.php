@extends('theme::layouts.app')

@section('title', $document->title . ' - ' . config('clan.name'))

@section('content')
    <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4"><a href="{{ route('clan-documents.index') }}" class="hover:underline">{{ __('nav.clan_documents') }}</a> &rarr; {{ $document->title }}</nav>
    <h1 class="text-2xl font-bold mb-4">{{ $document->title }}</h1>
    @if($document->content)
        <div class="prose dark:prose-invert max-w-none mb-4">{!! $document->content !!}</div>
    @endif
    @if($document->safe_file_url)
        <a href="{{ $document->safe_file_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded">{{ __('clan.download') }}</a>
    @endif
@endsection
