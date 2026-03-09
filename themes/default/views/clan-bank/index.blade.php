@extends('theme::layouts.app')

@section('title', __('nav.clan_bank') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{ __('nav.clan_bank') }}</h1>
    @if($categories->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">{{ __('clan.bank_empty') }}</p>
    @else
        @foreach($categories as $cat)
            <section class="mb-8">
                <h2 class="text-xl font-semibold mb-3">{{ $cat->name }}</h2>
                @if($cat->items->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('clan.no_items') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-200 dark:border-gray-700 rounded-lg">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th class="text-left p-2">{{ __('clan.item_name') }}</th>
                                    <th class="text-left p-2">{{ __('clan.quantity') }}</th>
                                    <th class="text-left p-2">{{ __('clan.location') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cat->items as $item)
                                    <tr class="border-t border-gray-200 dark:border-gray-700">
                                        <td class="p-2">
                                            <span class="font-medium">{{ $item->name }}</span>
                                            @if($item->description)<br><span class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($item->description, 60) }}</span>@endif
                                        </td>
                                        <td class="p-2">{{ $item->quantity }}</td>
                                        <td class="p-2">{{ $item->location ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        @endforeach
    @endif
@endsection
