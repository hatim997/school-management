@extends('layouts.master')

@section('title', __('Class Groups'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item active">
        {{ __('Class Groups') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top custom-datatables">
                    <thead>
                        <tr>
                            <th>{{ __('Sr.') }}</th>
                            <th>{{ __('Group Name') }}</th>
                            <th>{{ __('Subject') }}</th>
                            <th>{{ __('Students Assigned') }}</th>
                            @canany(['view class groups'])<th>{{ __('Action') }}</th>@endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($classGroups as $index => $classGroup)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $classGroup->name }}</td>
                                <td>{{ $classGroup->subject->name }}</td>
                                <td>{{ $classGroup->classGroupStudents ? count($classGroup->classGroupStudents) : 0 }}</td>
                                @canany(['view class groups'])
                                    <td class="d-flex">
                                        @canany(['view class groups'])
                                            <span class="text-nowrap">
                                                <a href="{{ route('dashboard.class-groups.show', $classGroup->id) }}"
                                                    class="btn btn-icon btn-text-warning waves-effect waves-light rounded-pill me-1 edit-order-btn"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('View Class Group Details') }}">
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
