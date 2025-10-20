@extends('layouts.master')

@section('title', __('Enroll a Child'))

@section('css')
    {{-- <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet"> --}}
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.children.index') }}">{{ __('Children') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Enroll a Child') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-6">
            <!-- Account -->
            <div class="card-body pt-4">
                <form method="POST" action="{{ route('dashboard.children.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row p-5">
                        <div class="mb-4 col-md-6">
                            <label for="name" class="form-label">{{ __('Name') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" id="name"
                                name="name" required placeholder="{{ __('Enter name') }}" autofocus
                                value="{{ old('name') }}" />
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-4 col-md-6">
                            <label for="email" class="form-label">{{ __('Email') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('email') is-invalid @enderror" type="email" id="email"
                                name="email" required placeholder="{{ __('Enter email') }}"
                                value="{{ old('email') }}" />
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-4 col-md-6">
                            <label for="dob" class="form-label">{{ __('Date of Birth') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('dob') is-invalid @enderror" type="date" id="dob"
                                name="dob" max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required placeholder="{{ __('Enter dob') }}" value="{{ old('dob') }}" />
                            @error('dob')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-4 col-md-6">
                            <label class="form-label" for="gender_id">{{ __('Gender') }}</label>
                            <select id="gender_id" name="gender_id"
                                class="select2 form-select @error('gender_id') is-invalid @enderror">
                                <option value="" selected disabled>{{ __('Select Gender') }}</option>
                                @if (isset($genders) && count($genders) > 0)
                                    @foreach ($genders as $gender)
                                        <option value="{{ $gender->id }}"
                                            {{ $gender->id == old('gender_id') ? 'selected' : '' }}>{{ $gender->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('gender_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-3">{{ __('Submit') }}</button>
                    </div>
                </form>
            </div>
            <!-- /Account -->
        </div>
    </div>
@endsection

@section('script')
    <!-- Vendors JS -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js" referrerpolicy="origin"></script> --}}
    <script>
        $(document).ready(function() {
            // tinymce.init({
            //     selector: '#description',
            //     height: 500,
            //     plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
            //     toolbar: `undo redo | formatselect | fontselect fontsizeselect |
        //               bold italic underline strikethrough forecolor backcolor |
        //               alignleft aligncenter alignright alignjustify |
        //               bullist numlist outdent indent | link image media table |
        //               removeformat | code fullscreen`,
            //     menubar: 'file edit view insert format tools table help',
            //     branding: false,
            //     content_style: "body { font-family:Helvetica,Arial,sans-serif; font-size:14px }"
            // });

            // Generate slug from name
            $('#title').on('keyup change', function() {
                let name = $(this).val();
                let slug = name.toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
                $('#slug').val(slug);
            });

            // Handle form submission manually to validate TinyMCE
            $('form').on('submit', function(e) {
                tinymce.triggerSave(); // sync content to <textarea>
                const $details = $('#description');
                const detailsContent = $details.val().trim();

                // Remove previous validation state
                $details.removeClass('is-invalid');
                $details.next('.invalid-feedback').remove();

                if (!detailsContent) {
                    e.preventDefault();

                    // Add Bootstrap invalid class
                    $details.addClass('is-invalid');

                    // Append validation message
                    $details.after(`
                        <div class="invalid-feedback">
                            {{ __('The details field is required.') }}
                        </div>
                    `);

                    // Optional: focus editor
                    tinymce.get('description').focus();
                }
            });
        });
    </script>
@endsection
