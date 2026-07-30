@extends('theme::layouts.app')

@section('title', site_name() . ' - ' . __('nav.home'))

@section('hero')
    @include('theme::partials.hero-media')
@endsection

@section('content')
    @include('theme::partials.home-content')
@endsection
