@extends('usercp.layout')

@section('usercp_content')
    <h1 class="text-2xl font-semibold mb-4">{{ __('crafting.my_requests') }}</h1>
    @if($requests->isEmpty())
        <p class="text-gray-600 dark:text-gray-400">{{ __('crafting.no_requests') }}</p>
        <a href="{{ route('crafting.create') }}" class="inline-block mt-4 px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium">{{ __('crafting.create_order') }}</a>
    @else
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 dark:border-gray-700 rounded-lg">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="text-left p-2">{{ __('crafting.item') }}</th>
                        <th class="text-left p-2">{{ __('crafting.custom_request') }}</th>
                        <th class="text-left p-2">{{ __('crafting.quantity') }}</th>
                        <th class="text-left p-2">{{ __('crafting.max_price') }}</th>
                        <th class="text-left p-2">{{ __('crafting.desired_date') }}</th>
                        <th class="text-left p-2">{{ __('crafting.priority') }}</th>
                        <th class="text-left p-2">{{ __('crafting.status') }}</th>
                        <th class="text-left p-2">{{ __('crafting.date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $req)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="p-2">{{ $req->craftableItem?->name ?? '–' }}</td>
                            <td class="p-2 text-sm text-gray-600 dark:text-gray-400">{{ $req->custom_request ? Str::limit($req->custom_request, 50) : '–' }}</td>
                            <td class="p-2 text-sm">{{ $req->quantity ?? 1 }}</td>
                            <td class="p-2 text-sm">{{ $req->max_price !== null ? number_format($req->max_price, 0, ',', '.') : '–' }}</td>
                            <td class="p-2 text-sm">{{ $req->desired_date?->format(__('general.date_format')) ?? '–' }}</td>
                            <td class="p-2 text-sm">{{ \App\Models\ItemRequest::priorityLabels()[$req->priority ?? 'normal'] ?? $req->priority }}</td>
                            <td class="p-2">{{ \App\Models\ItemRequest::statusLabels()[$req->status] ?? $req->status }}</td>
                            <td class="p-2 text-sm">{{ $req->created_at->format(__('general.date_format')) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $requests->links() }}</div>
        <a href="{{ route('crafting.create') }}" class="inline-block mt-4 px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium">{{ __('crafting.create_order') }}</a>
    @endif
@endsection
