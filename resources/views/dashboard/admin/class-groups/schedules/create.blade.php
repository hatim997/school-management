@extends('layouts.master')

@section('title', __('Create Schedule'))

@section('css')
    {{-- <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet"> --}}
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.admin-class-groups.index') }}">{{ __('Class Groups') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('dashboard.class-group-schedules.index', $classGroup->id) }}">{{ __('Schedules') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Create') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-6">
            <!-- Account -->
            <div class="card-body pt-4">
                <form method="POST" action="{{ route('dashboard.class-group-schedules.store', $classGroup->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row p-5">
                        <h3>{{ __('Add New Schedule') }}</h3>
                        <div class="mb-4 col-md-12">
                            <label class="form-label" for="day">{{ __('Day') }}</label>
                            <select id="day" name="day" class="select2 form-select @error('day') is-invalid @enderror">
                                <option value="monday" {{ old('day') == 'monday' ? 'selected' : '' }}>Monday</option>
                                <option value="tuesday" {{ old('day') == 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                                <option value="wednesday" {{ old('day') == 'wednesday' ? 'selected' : '' }}>Wednesday</option>
                                <option value="thursday" {{ old('day') == 'thursday' ? 'selected' : '' }}>Thursday</option>
                                <option value="friday" {{ old('day') == 'friday' ? 'selected' : '' }}>Friday</option>
                                <option value="saturday" {{ old('day') == 'saturday' ? 'selected' : '' }}>Saturday</option>
                                <option value="sunday" {{ old('day') == 'sunday' ? 'selected' : '' }}>Sunday</option>
                            </select>

                            @error('day')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-4 col-md-6">
                            <label for="start_time" class="form-label">{{ __('Start Time') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('start_time') is-invalid @enderror" type="time" id="start_time"
                                name="start_time" required placeholder="{{ __('Enter start time') }}"
                                value="{{ old('start_time') }}" />
                            @error('start_time')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-4 col-md-6">
                            <label for="end_time" class="form-label">{{ __('End Time') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('end_time') is-invalid @enderror" type="time" id="end_time"
                                name="end_time" required placeholder="{{ __('Enter end time') }}"
                                value="{{ old('end_time') }}" />
                            @error('end_time')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4 col-md-6">
                            <label for="zoom_link" class="form-label">{{ __('Zoom Link') }}</label>
                            <input class="form-control @error('zoom_link') is-invalid @enderror" type="text" id="zoom_link"
                                name="zoom_link" placeholder="{{ __('Enter zoom link') }}"  value="{{ old('zoom_link') }}"/>
                            @error('zoom_link')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-3">{{ __('Add Schedule') }}</button>
                    </div>
                </form>
            </div>
            <!-- /Account -->
        </div>
    </div>
@endsection

@section('script')
@endsection
