@extends('usercp.layout')

@section('usercp_content')
    <h1 class="usercp-title">{{ __('crafting.my_requests') }}</h1>
    @if($requests->isEmpty())
        <p class="usercp-muted">{{ __('crafting.no_requests') }}</p>
        <a href="{{ route('crafting.create') }}" class="usercp-btn-primary mt-4">{{ __('crafting.create_order') }}</a>
    @else
        <div class="usercp-card overflow-x-auto p-0">
            <table class="usercp-table">
                <thead>
                    <tr>
                        <th>{{ __('crafting.item') }}</th>
                        <th>{{ __('crafting.custom_request') }}</th>
                        <th>{{ __('crafting.quantity') }}</th>
                        <th>{{ __('crafting.max_price') }}</th>
                        <th>{{ __('crafting.desired_date') }}</th>
                        <th>{{ __('crafting.priority') }}</th>
                        <th>{{ __('crafting.status') }}</th>
                        <th>{{ __('crafting.date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $req)
                        <tr>
                            <td>{{ $req->craftableItem?->name ?? '–' }}</td>
                            <td class="usercp-muted text-sm">{{ $req->custom_request ? Str::limit($req->custom_request, 50) : '–' }}</td>
                            <td class="text-sm">{{ $req->quantity ?? 1 }}</td>
                            <td class="text-sm">{{ $req->max_price !== null ? number_format($req->max_price, 0, ',', '.') : '–' }}</td>
                            <td class="text-sm">{{ $req->desired_date?->format(__('general.date_format')) ?? '–' }}</td>
                            <td class="text-sm">{{ \App\Models\ItemRequest::priorityLabels()[$req->priority ?? 'normal'] ?? $req->priority }}</td>
                            <td>{{ \App\Models\ItemRequest::statusLabels()[$req->status] ?? $req->status }}</td>
                            <td class="text-sm">{{ $req->created_at->format(__('general.date_format')) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $requests->links() }}</div>
        <a href="{{ route('crafting.create') }}" class="usercp-btn-primary mt-4">{{ __('crafting.create_order') }}</a>
    @endif
@endsection
