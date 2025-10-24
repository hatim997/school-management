@extends('layouts.master')

@section('title', 'Dashboard')

@section('css')
@endsection

@section('breadcrumb-items')
    {{-- <li class="breadcrumb-item active">{{ __('Dashboard') }}</li> --}}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
            @role(['admin', 'super-admin'])
                @include('dashboard.admin.index')
            @endrole
            @role(['parent'])
                @include('dashboard.parents.index')
            @endrole
            @role(['student'])
                @include('dashboard.students.index')
            @endrole
            @role(['teacher'])
                @include('dashboard.teachers.index')
            @endrole
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/js/app-academy-dashboard.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Countdown logic
            $('.countdown-btn').each(function() {
                const $button = $(this);
                const startTime = new Date($button.data('start')).getTime();
                const $timerSpan = $button.find('.countdown-timer');

                const interval = setInterval(function() {
                    const now = new Date().getTime();
                    const distance = startTime - now;

                    if (distance <= 0) {
                        clearInterval(interval);
                        $button.removeClass('btn-warning').addClass('btn-success')
                            .prop('disabled', false).text('Join Now');
                        return;
                    }

                    const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
                    const minutes = Math.floor((distance / (1000 * 60)) % 60);
                    const seconds = Math.floor((distance / 1000) % 60);

                    $timerSpan.text(
                        `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
                    );
                }, 1000);
            });

            // Attendance + Join logic
            $('.join-btn').on('click', function() {
                const button = $(this);
                const classGroupId = button.data('class-group-id');
                const zoomLink = button.data('zoom-link');

                button.prop('disabled', true).text('Marking attendance...');

                $.ajax({
                    url: "{{ route('dashboard.students.attendance.mark') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        class_group_id: classGroupId
                    },
                    success: function(response) {
                        if (response.success) {
                            button.text('Attendance Marked ✅');
                            window.open(zoomLink, '_blank');
                            setTimeout(() => {
                                button.text('Join Now').prop('disabled', false);
                            }, 5000);
                        } else {
                            button.text('Error, Try Again').prop('disabled', false);
                        }
                    },
                    error: function() {
                        button.text('Error, Try Again').prop('disabled', false);
                    }
                });
            });
        });
    </script>

@endsection
