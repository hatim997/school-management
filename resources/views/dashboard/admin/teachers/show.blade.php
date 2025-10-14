@extends('layouts.master')

@section('title', __('Teacher Details'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{route('dashboard.teachers.index')}}">{{ __('Teachers') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Details') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- User Sidebar -->
            <div class="col-xl-4 col-lg-5 order-1 order-md-0">
                <!-- User Card -->
                <div class="card mb-6">
                    <div class="card-body pt-12">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                <img class="img-fluid rounded mb-4"
                                    src="{{ asset($teacher->profile->profile_image ?? 'assets/img/default/user.png') }}"
                                    height="120" width="120" alt="User avatar" />
                                <div class="user-info text-center">
                                    <h5>{{ $teacher->name }}</h5>
                                </div>
                            </div>
                        </div>
                        <h5 class="pb-4 border-bottom mb-4">Details</h5>
                        <div class="info-container">
                            <ul class="list-unstyled mb-6">
                                <li class="mb-2">
                                    <span class="h6">Username:</span>
                                    <span>{{ '@' . $teacher->username }}</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Email:</span>
                                    <span>{{ $teacher->email }}</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Qualification:</span>
                                    <span>{{ $teacher->profile->qualification }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /User Card -->
            </div>
            <!--/ User Sidebar -->

            <!-- User Content -->
            <div class="col-xl-8 col-lg-7 order-0 order-md-1">
                <!-- Assigned Groups -->
                <div class="card card-action mb-6">
                    <div class="card-header align-items-center">
                        <h5 class="card-action-title mb-0">Assigned Groups</h5>
                    </div>
                    <div class="card-body">
                        <div class="added-cards">
                            <div class="row row-gap-4 row-gap-xl-0">
                                @if (isset($classGroups) && count($classGroups) > 0)
                                    @foreach ($classGroups as $classGroup)
                                        <div class="col-xl-12 order-0 order-xl-0">
                                            <div class="cardMaster border p-6 rounded mb-4">
                                                <div class="d-flex justify-content-between flex-sm-row flex-column">
                                                    <div class="card-information d-flex gap-2">
                                                        <img class="mb-2 img-fluid" style="height: 40px"
                                                            src="{{ $classGroup->subject->image ? asset($classGroup->subject->image) : asset('uploads/subjects/subject-default-image.jpg') }}"
                                                            alt="Master Card" />
                                                        <div class="d-flex align-items-center mb-2">
                                                            <h6 class="mb-0 me-2">{{ $classGroup->name }}</h6>
                                                            <span
                                                                class="badge bg-label-primary me-1">{{ $classGroup->subject->name }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column text-start text-lg-end">
                                                        <small
                                                            class="mt-sm-4 mt-2 order-sm-1 order-0 text-sm-end mb-2">Joined
                                                            at
                                                            {{ $classGroup->created_at->format('M d, Y') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Assigned Groups -->
            </div>
            <!--/ User Content -->
        </div>
        <!-- /Modal -->
    </div>
@endsection

@section('script')
    {{-- <script src="{{asset('assets/js/app-user-list.js')}}"></script> --}}
    <script>
        $(document).ready(function() {
            //
        });
    </script>
@endsection
