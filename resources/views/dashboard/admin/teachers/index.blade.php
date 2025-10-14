@extends('layouts.master')

@section('title', __('Teachers'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item active">
        {{ __('Teachers') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Books List Table -->
        <div class="card">
            <div class="card-header">
                {{-- @canany(['create teacher'])
                    <a href="{{ route('dashboard.teachers.create') }}" class="add-new btn btn-primary waves-effect waves-light">
                        <i class="ti ti-school me-0 me-sm-1 ti-xs"></i><span
                            class="d-none d-sm-inline-block">{{ __('Enroll a Teac') }}</span>
                    </a>
                @endcan --}}
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top custom-datatables">
                    <thead>
                        <tr>
                            <th>{{ __('Sr.') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Qualification') }}</th>
                            <th>{{ __('Status') }}</th>
                            @canany(['delete teacher', 'update teacher'])<th>{{ __('Action') }}</th>@endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($teachers as $index => $teacher)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $teacher->name }}</td>
                                <td>{{ $teacher->email }}</td>
                                <td>{{ $teacher->profile->qualifications }}</td>
                                <td>
                                    @if ($teacher->is_active === 'pending')
                                        <a href="{{ route('dashboard.teachers.approve', $teacher->id) }}"
                                            class="btn btn-sm btn-warning rounded-pill px-3" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Approve Teacher Request') }}">
                                            <i class="ti ti-check me-1"></i> Approve
                                        </a>
                                    @elseif ($teacher->is_active === 'active')
                                        <span class="badge bg-label-success rounded-pill">Active</span>
                                    @elseif ($teacher->is_active === 'inactive')
                                        <span class="badge bg-label-danger rounded-pill">Inactive</span>
                                    @else
                                        <span class="badge bg-label-secondary rounded-pill">Unknown</span>
                                    @endif
                                </td>
                                @canany(['update teacher'])
                                    <td class="d-flex">
                                        @canany(['update teacher'])
                                            <span class="text-nowrap">
                                                <a href="{{ route('dashboard.teachers.show', $teacher->id) }}"
                                                    class="btn btn-icon btn-text-warning waves-effect waves-light rounded-pill me-1 edit-order-btn"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('View Teacher Details') }}">
                                                    <i class="ti ti-eye ti-md"></i>
                                                </a>
                                            </span>
                                        @endcan
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
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
