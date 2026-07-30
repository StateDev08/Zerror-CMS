@extends('theme::layouts.app')

@section('title', __('nav.clan_treasury') . ' - ' . site_name())

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{ __('nav.clan_treasury') }}</h1>

    <div class="grid gap-3 sm:grid-cols-3 mb-8">
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('clan.treasury_income') }}</p>
            <p class="text-xl font-semibold text-green-700 dark:text-green-400">{{ number_format($totalIncome, 2, ',', '.') }} €</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('clan.treasury_expense') }}</p>
            <p class="text-xl font-semibold text-red-700 dark:text-red-400">{{ number_format($totalExpense, 2, ',', '.') }} €</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('clan.treasury_balance') }}</p>
            <p class="text-xl font-semibold">{{ number_format($balance, 2, ',', '.') }} €</p>
        </div>
    </div>

    @if($categories->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">{{ __('clan.treasury_empty') }}</p>
    @else
        @foreach($categories as $cat)
            <section class="mb-8">
                <h2 class="text-xl font-semibold mb-3">{{ $cat->name }}</h2>
                @if($cat->entries->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('clan.no_entries') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-200 dark:border-gray-700 rounded-lg">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th class="text-left p-2">{{ __('clan.treasury_date') }}</th>
                                    <th class="text-left p-2">{{ __('clan.treasury_type') }}</th>
                                    <th class="text-left p-2">{{ __('clan.treasury_title') }}</th>
                                    <th class="text-right p-2">{{ __('clan.treasury_amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cat->entries as $entry)
                                    <tr class="border-t border-gray-200 dark:border-gray-700">
                                        <td class="p-2">{{ $entry->entry_date?->format(__('general.date_format')) ?? '—' }}</td>
                                        <td class="p-2">
                                            @if($entry->type === 'income')
                                                <span class="text-green-700 dark:text-green-400">{{ __('clan.treasury_income') }}</span>
                                            @else
                                                <span class="text-red-700 dark:text-red-400">{{ __('clan.treasury_expense') }}</span>
                                            @endif
                                        </td>
                                        <td class="p-2">
                                            <span class="font-medium">{{ $entry->title ?: '—' }}</span>
                                            @if($entry->note)<br><span class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($entry->note, 60) }}</span>@endif
                                        </td>
                                        <td class="p-2 text-right font-medium">{{ number_format((float) $entry->amount, 2, ',', '.') }} €</td>
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
