@extends('theme::layouts.app')

@section('title', __('nav.roster') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="page-title mb-8">{{ __('nav.roster') }}</h1>
    @if($ranks->isEmpty())
        <p class="text-neutral-500 dark:text-neutral-400 rounded-2xl bg-neutral-100/80 dark:bg-neutral-800/50 p-8 text-center">Noch keine Mitglieder eingetragen.</p>
    @else
        @foreach($ranks as $rank)
            @php $rankMembers = $members->get((string) $rank->id, collect()); @endphp
            @if($rankMembers->isNotEmpty())
                <section class="mb-10">
                    <h2 class="text-xl font-semibold mb-4" @if($rank->color) style="color: {{ $rank->color }}" @endif>{{ $rank->name }}</h2>
                    <ul class="space-y-2">
                        @foreach($rankMembers as $member)
                            <li class="card flex items-center gap-4">
                                @if($member->avatar)
                                    <img src="{{ storage_asset($member->avatar) }}" alt="" class="w-12 h-12 rounded-xl object-cover ring-2 ring-neutral-200/80 dark:ring-neutral-600/80">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-neutral-200 dark:bg-neutral-700 flex items-center justify-center text-neutral-500 dark:text-neutral-400 font-semibold text-lg">{{ mb_substr($member->display_name, 0, 1) }}</div>
                                @endif
                                <div>
                                    <span class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $member->display_name }}</span>
                                    @if($member->position)
                                        <span class="text-neutral-500 dark:text-neutral-400 ml-2">– {{ $member->position }}</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        @endforeach
    @endif
@endsection
