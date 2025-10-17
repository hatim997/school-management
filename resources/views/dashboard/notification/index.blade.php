@extends('layouts.master')

@section('title', __('Notifications'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item active">{{ __('Notifications') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6 mb-5">
            <!-- Notification Style -->
            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="fw-medium">Notifications</small>
                    <div>
                        <button id="markAllReadBtn" class="btn btn-text-success rounded-pill btn-icon" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Mark all as read">
                            <i class="ti ti-mail-opened"></i>
                        </button>
                        <button id="deleteAllBtn" class="btn btn-text-danger rounded-pill btn-icon" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Delete all Notifications">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group">
                        @if (isset($notifications) && count($notifications) > 0)
                            @foreach ($notifications as $notification)
                                <a href="{{ route('notification.click', $notification->id) }}"
                                    class="list-group-item list-group-item-action flex-column align-items-start {{ $notification->read_at == null ? 'active' : '' }}">
                                    <div class="d-flex justify-content-between w-100">
                                        <h5 class="mb-1">
                                            {{ $notification->table_name ? ucfirst($notification->table_name) : 'Notification' }}
                                        </h5>
                                        <div>
                                            <!-- Inside your foreach -->
                                            @if ($notification->read_at == null)
                                                <button class="markReadBtn btn btn-text-success rounded-pill btn-icon"
                                                    data-notification-id="{{ $notification->id }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Mark as read">
                                                    <i class="ti ti-mail-opened"></i>
                                                </button>
                                            @endif
                                            <button class="deleteBtn btn btn-text-danger rounded-pill btn-icon"
                                                data-notification-id="{{ $notification->id }}" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>

                                            <small>{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    <p class="mb-1">
                                        {{ $notification->message }}
                                    </p>
                                </a>
                            @endforeach
                        @else
                                <p>No Notifications</p>
                        @endif
                    </div>
                </div>
            </div>
            <!--/ Notification Style -->
        </div>
    </div>


@endsection

@section('script')
    <script>
        $(document).ready(function() {

            // Mark all notifications as read
            $('#markAllReadBtn').click(function() {
                $.ajax({
                    url: '{{ route('notifications.markAllAsRead') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        location.reload(); // Reload page after success
                    },
                    error: function(xhr) {
                        alert('Something went wrong!');
                    }
                });
            });

            // Delete all notifications
            $('#deleteAllBtn').click(function(event) {
                event.preventDefault();
                var href = $(this).attr('href');

                Swal.fire({
                    title: '{{ __('Are you sure?') }}',
                    text: '{{ __('You would not be able to revert this!') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: '{{ __('Cancel') }}',
                    confirmButtonText: '{{ __('Yes, delete it!') }}',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('notifications.deleteAll') }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: '{{ __('Success!') }}',
                                    text: "{{ __('All Notifications deleted successfully!') }}",
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                location.reload();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: '{{ __('Error!') }}',
                                    text: "{{ __('Something went wrong! try again later') }}",
                                    icon: "error",
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: "{{ __('Your data is safe!') }}",
                            icon: "info",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // Mark a single notification as read
            $(document).on('click', '.markReadBtn', function() {
                var notificationId = $(this).data('notification-id');
                var url = '{{ route('notifications.markAsRead', ':id') }}';
                url = url.replace(':id', notificationId);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Something went wrong!');
                    }
                });
            });

            // Delete a single notification
            $(document).on('click', '.deleteBtn', function() {
                var notificationId = $(this).data('notification-id');

                var url = '{{ route('notifications.delete', ':id') }}';
                url = url.replace(':id', notificationId);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Something went wrong!');
                    }
                });
            });


        });
    </script>
@endsection
