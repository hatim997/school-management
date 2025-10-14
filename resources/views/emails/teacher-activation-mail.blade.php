@extends('layouts.mails.master')

@section('title', 'Your Teacher Account Has Been Activated')

@section('css')
@endsection

@section('content')
    <p>{{ __('Hi') }} <strong>{{ $teacher->name }}</strong>,</p>
    <p>{{ __("Your teacher account has been successfully activated. You can now log in and start using the dashboard.") }}</p>

    <p class="mt-3">{{ __('Log in and explore all the amazing features waiting for you. If you have any questions or need assistance, our team is here to help!') }}</p>

    <a href="{{ route('dashboard') }}" class="cta-button">{{ __('Go to Dashboard') }}</a>
@endsection

@section('script')
@endsection
