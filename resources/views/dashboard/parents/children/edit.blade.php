@extends('layouts.master')

@section('title', __('Edit Book Law'))

@section('css')
    {{-- <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet"> --}}
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.books.index') }}">{{ __('Books') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('dashboard.book-laws.index', $book->id) }}">{{ __('Book Laws') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Edit') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-6">
            <!-- Account -->
            <div class="card-body pt-4">
                <form method="POST" action="{{ route('dashboard.book-laws.update', $bookLaw->id) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row p-5">
                        <h3>{{ __('Edit Book Law') }}</h3>
                        <div class="mb-4 col-md-6">
                            <label for="title" class="form-label">{{ __('Title') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('title') is-invalid @enderror" type="text" id="title"
                                name="title" required placeholder="{{ __('Enter title') }}" autofocus
                                value="{{ old('title', $bookLaw->title) }}" />
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-4 col-md-6">
                            <label for="slug" class="form-label">{{ __('Slug') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('slug') is-invalid @enderror" type="text" id="slug"
                                name="slug" required placeholder="{{ __('Enter slug') }}" value="{{ old('slug', $bookLaw->slug) }}" />
                            @error('slug')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-4 col-md-12">
                            <label for="content" class="form-label">{{ __('content') }}</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="description" name="content"
                                placeholder="{{ __('Enter content') }}" cols="30" rows="10">{{ old('content', $bookLaw->content) }}</textarea>
                            @error('content')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-3">{{ __('Edit Book Law') }}</button>
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
