@extends('layouts.master')

@section('title', __('Class Group Materials'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.class-groups.index') }}">{{ __('Class Groups') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Materials') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                <button class="add-new btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                    data-bs-target="#basicModal">
                    <i class="ti ti-folder-open me-0 me-sm-1 ti-xs"></i><span
                        class="d-none d-sm-inline-block">{{ __('Add Material') }}</span>
                </button>
            </div>
            <div class="card-body">
                <div class="card-datatable table-responsive" style="overflow-x: hidden;">
                    @if (isset($classGroupMaterials) && count($classGroupMaterials) > 0)
                        <div class="row g-3">
                            @foreach ($classGroupMaterials as $material)
                                @php
                                    // Detect file type for icon
                                    $icon = 'ti ti-file';
                                    if (Str::contains($material->file_type, 'pdf')) {
                                        $icon = 'ti ti-file-text text-danger';
                                    } elseif (Str::contains($material->file_type, 'image')) {
                                        $icon = 'ti ti-photo text-success';
                                    } elseif (Str::contains($material->file_type, 'word')) {
                                        $icon = 'ti ti-file-description text-primary';
                                    } elseif (
                                        Str::contains($material->file_type, 'excel') ||
                                        Str::contains($material->file_type, 'spreadsheet')
                                    ) {
                                        $icon = 'ti ti-file-spreadsheet text-success';
                                    } elseif (Str::contains($material->file_type, 'video')) {
                                        $icon = 'ti ti-video text-warning';
                                    } elseif (Str::contains($material->file_type, 'audio')) {
                                        $icon = 'ti ti-music text-info';
                                    } elseif (Str::contains($material->file_type, 'zip')) {
                                        $icon = 'ti ti-file-zip text-secondary';
                                    }
                                @endphp

                                <div class="col-sm-6 col-lg-4">
                                    <div class="card p-3 h-100 border shadow-sm">
                                        <div class="text-center mb-3">
                                            <i class="{{ $icon }}" style="font-size: 48px;"></i>
                                        </div>

                                        <div class="card-body p-2">
                                            <h6 class="fw-bold text-primary mb-2 text-truncate">{{ $material->file_name }}
                                            </h6>

                                            <ul class="list-unstyled small mb-3">
                                                <li><strong>Type:</strong> {{ $material->file_type }}</li>
                                                <li><strong>Size:</strong>
                                                    {{ \App\Helpers\Helper::humanReadableSize($material->file_size) }}</li>
                                                <li><strong>Uploaded By:</strong> {{ $material->user->name ?? 'Unknown' }}
                                                </li>
                                                <li><strong>Date:</strong> {{ $material->created_at->format('d M, Y') }}
                                                </li>
                                            </ul>

                                            <a href="{{ asset($material->file) }}" target="_blank"
                                                class="btn btn-sm btn-primary w-100">
                                                <i class="ti ti-eye me-1"></i> Preview / Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ti ti-folder-off text-muted" style="font-size: 48px;"></i>
                            <p class="mt-2 text-muted">No Materials Available</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add Material</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('dashboard.class-groups.materials.store', $classGroup->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-4">
                                <label for="file_name" class="form-label">{{ __('File Name') }}</label><span
                                    class="text-danger">*</span>
                                <input class="form-control @error('file_name') is-invalid @enderror" type="text"
                                    id="file_name" name="file_name" required placeholder="{{ __('Enter file name') }}"
                                    autofocus value="{{ old('file_name') }}" />
                                @error('file_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col mb-0">
                                <label for="file" class="form-label">{{ __('File') }}</label><span
                                    class="text-danger">*</span>
                                <input class="form-control @error('file') is-invalid @enderror" type="file"
                                    id="file" name="file" required />
                                @error('file')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            //
        });
    </script>
@endsection
