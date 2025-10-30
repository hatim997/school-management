@extends('layouts.master')

@section('title', __('Children'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item active">{{ __('Children') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Books List Table -->
        <div class="card">
            <div class="card-header">
                @canany(['create children'])
                    <a href="{{ route('dashboard.children.create') }}" class="add-new btn btn-primary waves-effect waves-light">
                        <i class="ti ti-school me-0 me-sm-1 ti-xs"></i><span
                            class="d-none d-sm-inline-block">{{ __('Enroll a Children') }}</span>
                    </a>
                @endcan
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top custom-datatables">
                    <thead>
                        <tr>
                            <th>{{ __('Sr.') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Username') }}</th>
                            <th>{{ __('Age') }}</th>
                            @canany(['delete children', 'update children'])<th>{{ __('Action') }}</th>@endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($childrens as $index => $children)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $children->child->name }}</td>
                                <td>{{ '@'.$children->child->username }}</td>
                                <td>{{ $children->child->profile->age }}</td>
                                @canany(['delete children', 'update children'])
                                    <td class="d-flex">
                                        @canany(['delete children'])
                                            <form action="{{ route('dashboard.children.destroy', $children->id) }}" method="POST">
                                                @method('DELETE')
                                                @csrf
                                                <a href="#" type="submit"
                                                    class="btn btn-icon btn-text-danger waves-effect waves-light rounded-pill delete-record delete_confirmation"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Delete Children') }}">
                                                    <i class="ti ti-trash ti-md"></i>
                                                </a>
                                            </form>
                                        @endcan
                                        @canany(['update children'])
                                            <span class="text-nowrap">
                                                <a href="{{ route('dashboard.children.show', $children->id) }}"
                                                    class="btn btn-icon btn-text-warning waves-effect waves-light rounded-pill me-1 edit-order-btn"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('View Child Details') }}">
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
