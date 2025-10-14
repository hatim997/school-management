@extends('layouts.errors.master')

@section('title', 'Account Pending Approval')

@section('css')
@endsection

@section('content')
    <div class="misc-wrapper text-center">
        <h1 class="mb-2 mx-2 text-warning" style="line-height: 6rem; font-size: 6rem">⏳</h1>
        <h4 class="mb-2 mx-2">{{ __('Your Account is Pending Approval') }}</h4>
        <p class="mb-6 mx-2">
            {{ __('Thank you for signing up! Your account is currently under review.
            Our team is verifying your information to ensure everything is in order.
            You’ll receive an email notification once your account has been approved.') }}
        </p>
        <p class="mb-6 mx-2 text-muted">
            {{ __('If you have any questions or need assistance, please reach out to our support team.') }}
        </p>

        <div class="d-flex justify-content-center gap-3">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-outline-danger mb-10">
                {{ __('Logout') }}
            </a>
            <a href="mailto:support@yourdomain.com" class="btn btn-primary mb-10">
                {{ __('Contact Support') }}
            </a>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>

        <div class="mt-4">
            <img src="{{ asset('assets/img/illustrations/page-misc-error.png') }}"
                 alt="Account Pending Approval"
                 width="225"
                 class="img-fluid" />
        </div>
    </div>
@endsection

@section('script')
@endsection
