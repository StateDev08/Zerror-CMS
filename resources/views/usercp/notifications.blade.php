@extends('usercp.layout')

@section('usercp_content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <h1 class="usercp-title" style="margin:0">{{ __('usercp.notifications') }}</h1>
        @if($notifications->whereNull('read_at')->count() > 0 || $notifications->total() > 0)
            <form action="{{ route('usercp.notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="usercp-btn-primary">{{ __('usercp.notifications_mark_all_read') }}</button>
            </form>
        @endif
    </div>

    @if($notifications->isEmpty())
        <p class="usercp-muted">{{ __('usercp.notifications_empty') }}</p>
    @else
        <ul class="space-y-2 list-none m-0 p-0">
            @foreach($notifications as $notification)
                <li class="usercp-card" style="{{ $notification->read_at ? 'opacity:.8' : '' }}">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium m-0">{{ $notification->message }}</p>
                            <p class="text-sm usercp-muted mt-1 mb-0">{{ $notification->created_at->format(__('general.date_format') . ' H:i') }}</p>
                            @if($notification->link)
                                <a href="{{ route('usercp.notifications.read', $notification) }}" class="inline-block mt-2 text-sm">{{ __('usercp.notifications_open') }}</a>
                            @endif
                        </div>
                        @if(! $notification->read_at)
                            <form action="{{ route('usercp.notifications.read', $notification) }}" method="POST">
                                @csrf
                                <button type="submit" class="usercp-btn">{{ __('usercp.notifications_mark_read') }}</button>
                            </form>
                        @else
                            <span class="text-xs usercp-muted">{{ __('usercp.notifications_read') }}</span>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="mt-4">{{ $notifications->links() }}</div>
    @endif
@endsection
