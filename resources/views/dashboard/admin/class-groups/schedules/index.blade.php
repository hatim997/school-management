@extends('layouts.master')

@section('title', __('Class Group Schedules'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.admin-class-groups.index') }}">{{ __('Class Groups') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Class Group Schedules') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                @canany(['create class group schedules'])
                    <a href="{{ route('dashboard.class-group-schedules.create', $classGroup->id) }}"
                        class="add-new btn btn-primary waves-effect waves-light">
                        <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span
                            class="d-none d-sm-inline-block">{{ __('Add New Schedule') }}</span>
                    </a>
                @endcan
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top custom-datatables">
                    <thead>
                        <tr>
                            <th>{{ __('Sr.') }}</th>
                            <th>{{ __('Day') }}</th>
                            <th>{{ __('Start Time') }}</th>
                            <th>{{ __('End Time') }}</th>
                            <th>{{ __('Join Link') }}</th>
                            @canany(['delete class group schedules', 'update class group schedules'])<th>
                                {{ __('Action') }}</th>@endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($classGroupSchedules as $index => $classGroupSchedule)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ ucwords($classGroupSchedule->day) }}</td>
                                <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $classGroupSchedule->start_time)->format('h:i A') }}</td>
                                <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $classGroupSchedule->end_time)->format('h:i A') }}</td>
                                <td>
                                    @if ($classGroupSchedule->zoom_link)
                                        <a href="{{ $classGroupSchedule->zoom_link }}"
                                            class="btn btn-sm btn-primary rounded-pill px-3" target="_blank" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="{{ __('Join Zoom Link') }}">
                                            Join Now
                                        </a>
                                    @else
                                        No Link Available
                                    @endif
                                </td>
                                @canany(['delete class group schedules', 'update class group schedules'])
                                    <td class="d-flex">
                                        @canany(['delete class group schedules'])
                                            <form
                                                action="{{ route('dashboard.class-group-schedules.destroy', $classGroupSchedule->id) }}"
                                                method="POST">
                                                @method('DELETE')
                                                @csrf
                                                <a href="#" type="submit"
                                                    class="btn btn-icon btn-text-danger waves-effect waves-light rounded-pill delete-record delete_confirmation"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Delete Class Group Schedule') }}">
                                                    <i class="ti ti-trash ti-md"></i>
                                                </a>
                                            </form>
                                        @endcan
                                        @canany(['update class group schedules'])
                                            <span class="text-nowrap">
                                                <a href="{{ route('dashboard.class-group-schedules.edit', $classGroupSchedule->id) }}"
                                                    class="btn btn-icon btn-text-success waves-effect waves-light rounded-pill me-1 edit-order-btn"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Edit Class Group Schedule') }}">
                                                    <i class="ti ti-edit ti-md"></i>
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
