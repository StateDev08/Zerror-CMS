@extends('theme::layouts.app')

@section('title', $thread->title . ' - ' . config('clan.name'))

@section('content')
    <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        <a href="{{ route('forum.index') }}" class="hover:underline">{{ __('nav.forum') }}</a> /
        <a href="{{ route('forum.show', $thread->forum) }}" class="hover:underline">{{ $thread->forum->name }}</a> /
        {{ $thread->title }}
    </nav>
    <h1 class="text-2xl font-bold mb-4">{{ $thread->title }}</h1>
    @if(session('success'))
        <p class="mb-4 p-2 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded text-sm">{{ __('forum.post_created') }}</p>
    @endif
    <div class="space-y-4">
        @foreach($posts as $post)
            <article id="post-{{ $post->id }}" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-2">
                    <span>{{ $post->user?->name ?? __('forum.guest') }}</span>
                    <div class="flex items-center gap-2">
                        <span>{{ $post->created_at->format(__('general.date_format')) }}</span>
                        @if(auth()->check() && !$thread->locked)
                            <button type="button" class="forum-quote-btn text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 text-xs" data-quote-author="{{ e($post->user?->name ?? __('forum.guest')) }}" data-quote-body="{{ e($post->body) }}">{{ __('forum.quote') }}</button>
                        @endif
                    </div>
                </div>
                <div class="prose dark:prose-invert max-w-none">
                    {!! \App\Support\ForumFormat::quoteToHtml($post->body) !!}
                </div>
            </article>
        @endforeach
    </div>
    {{ $posts->links() }}
    @if(auth()->check() && !$thread->locked)
        <form id="reply-form" action="{{ route('forum.thread.reply', $thread) }}" method="POST" class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
            @csrf
            <label for="body" class="block font-medium mb-2">{{ __('forum.reply') }}</label>
            <textarea name="body" id="reply-body" rows="4" required class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-800 px-3 py-2"></textarea>
            @error('body')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            <button type="submit" class="mt-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded">{{ __('forum.submit_reply') }}</button>
        </form>
        <script>
            document.querySelectorAll('.forum-quote-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var author = this.getAttribute('data-quote-author');
                    var body = this.getAttribute('data-quote-body');
                    var quote = '[quote author="' + author.replace(/"/g, '&quot;') + '"]\n' + body + '\n[/quote]\n\n';
                    var ta = document.getElementById('reply-body');
                    ta.value = quote + ta.value;
                    ta.focus();
                    document.getElementById('reply-form').scrollIntoView({ behavior: 'smooth' });
                });
            });
        </script>
    @endif
@endsection
