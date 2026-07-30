@extends('theme::layouts.app')

@section('title', site_name() . ' - ' . __('nav.home'))

@section('hero')
<section class="relative flex flex-col mb-6">
    @include('theme::partials.hero-media')
</section>
@endsection

@section('content')
    @include('theme::partials.home-content')
@endsection
