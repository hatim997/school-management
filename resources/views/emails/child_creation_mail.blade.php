@extends('layouts.mails.master')

@section('title', 'Child Account Creation Successful')

@section('css')
@endsection

@section('content')
    <p>{{ __('Hi') }} <strong>{{ $childData->parent_name }}</strong>,</p>
    <p>{{ __("Your child's account has been created successfully! Below are your child's account credentials to help you get started:") }}</p>

    <div class="credentials">
        <h3>{{ $childData->child_name }}'s {{ __('Login Credentials:') }}</h3>
        <p><strong>{{ __('Email:') }}</strong> {{ $childData->child_email }}</p>
        <p><strong>{{ __('Temporary Password:') }}</strong> {{ $childData->temp_pass }}</p>
    </div>

    {{-- ✅ Added line --}}
    <p style="color: #d9534f; font-weight: bold;">
        {{ __('For your security, please update the password as soon as possible after logging in.') }}
    </p>

    <p class="mt-3">{{ __('Log in and explore all the amazing features waiting for you. If you have any questions or need assistance, our team is here to help!') }}</p>

    <a href="{{ route('dashboard') }}" class="cta-button">{{ __('Go to Dashboard') }}</a>
@endsection

@section('script')
@endsection
