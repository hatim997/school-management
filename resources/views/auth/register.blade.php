@extends('layouts.authentication.master')
@section('title', 'Registration')

@section('css')
@endsection

@section('content')
    <h5 class="mb-1">Adventure starts here 🚀</h5>
    <p class="mb-6">Make your app management easy and fun!</p>

    <form id="formAuthentication" class="mb-6" action="{{ route('register.attempt') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="form-label" for="role">{{ __('Register as') }}</label>
            <select id="role" name="role" class="select2 form-select @error('role') is-invalid @enderror">
                <option value="parent" {{ old('role') == 'parent' ? 'selected' : 'selected' }}>
                    {{ __('Parent') }}
                </option>
                <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>
                    {{ __('Teacher') }}
                </option>
            </select>
            @error('book_type_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="mb-6">
            <label for="name" class="form-label">{{ __('Name') }}</label><span class="text-danger">*</span>
            <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                id="name" name="name" placeholder="{{ __('Enter your name') }}" autofocus required />
            @error('name')
                <span class="invalid-feedback" role="alert">
                    {{ $message }}
                </span>
            @enderror
        </div>
        <div class="mb-6">
            <label for="email" class="form-label">{{ __('Email') }}</label><span class="text-danger">*</span>
            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                value="{{ old('email') }}" placeholder="{{ __('Enter your email') }}" required />
            @error('email')
                <span class="invalid-feedback" role="alert">
                    {{ $message }}
                </span>
            @enderror
        </div>
        <div class="mb-6 form-password-toggle">
            <label class="form-label" for="password">{{ __('Password') }}</label><span class="text-danger">*</span>
            <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" required />
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
            </div>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    {{ $message }}
                </span>
            @enderror
        </div>
        <div class="mb-6 form-password-toggle">
            <label class="form-label" for="confirm-password">{{ __('Confirm Password') }}</label><span
                class="text-danger">*</span>
            <div class="input-group input-group-merge">
                <input type="password" id="confirm-password"
                    class="form-control @error('confirm-password') is-invalid @enderror" name="confirm-password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="confirm-password" required />
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
            </div>
            @error('confirm-password')
                <span class="invalid-feedback" role="alert">
                    {{ $message }}
                </span>
            @enderror
        </div>
        <!-- Qualification -->
        <div class="mb-6 teacher-fields d-none">
            <label for="qualifications" class="form-label">{{ __('Qualification') }}</label><span
                class="text-danger">*</span>
            <input type="text" class="form-control @error('qualifications') is-invalid @enderror"
                value="{{ old('qualifications') }}" id="qualifications" name="qualifications"
                placeholder="{{ __('Enter your qualifications') }}" />
            @error('qualifications')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <!-- Subjects -->
        <div class="mb-4 teacher-fields d-none">
            <label class="form-label" for="subject_id">{{ __('Subjects') }}</label><span class="text-danger">*</span>
            <select id="subject_id" name="subject_id[]" multiple
                class="select2 form-select @error('subject_id.*') is-invalid @enderror">
                @if (isset($subjects) && count($subjects) > 0)
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}"
                            {{ collect(old('subject_id', []))->contains($subject->id) ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                @endif
            </select>
            @error('subject_id.*')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>


        <div class="mb-6 mt-8">
            <div class="form-check mb-8 ms-2">
                <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms-conditions"
                    name="terms" required {{ old('terms') == 'on' ? 'checked' : '' }} />
                <label class="form-check-label" for="terms-conditions">
                    {{ __('I agree to') }} <a href="javascript:void(0);">{{ __('privacy policy & terms') }}</a>
                </label>
            </div>
            @error('terms')
                <span class="invalid-feedback" role="alert">
                    {{ $message }}
                </span>
            @enderror
        </div>
        <div class="mb-6">
            @if (config('captcha.version') === 'v3')
                {!! \App\Helpers\Helper::renderRecaptcha('formAuthentication', 'register') !!}
            @elseif(config('captcha.version') === 'v2')
                <div class="form-field-block">
                    {!! app('captcha')->display() !!}
                    @if ($errors->has('g-recaptcha-response'))
                        <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
                    @endif
                </div>
            @endif
        </div>
        <button type="submit" class="btn btn-primary d-grid w-100">{{ __('Sign up') }}</button>
    </form>

    <p class="text-center">
        <span>Already have an account?</span>
        <a href="{{ route('login') }}">
            <span>Sign in instead</span>
        </a>
    </p>
@endsection

@section('script')
    {!! NoCaptcha::renderJs() !!}

    <script>
        $(document).ready(function() {
            const roleSelect = $('#role');
            const teacherFields = $('.teacher-fields');
            const qualificationInput = $('#qualifications');
            const subjectSelect = $('#subject_id');

            function toggleTeacherFields() {
                const isTeacher = roleSelect.val() === 'teacher';

                // Show/hide teacher-only fields
                teacherFields.toggleClass('d-none', !isTeacher);

                // Add/remove "required" dynamically
                qualificationInput.prop('required', isTeacher);
                subjectSelect.prop('required', isTeacher);
            }

            // Initial check (for old form values)
            toggleTeacherFields();

            // Re-run when role changes (Select2 event-safe)
            roleSelect.on('change.select2', toggleTeacherFields);
        });
    </script>

@endsection
