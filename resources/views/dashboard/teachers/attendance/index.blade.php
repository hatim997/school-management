@extends('layouts.master')

@section('title', __('Attendance'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item active">
        {{ __('Attendance') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">My Class Group Attendances</h5>
                <div class="d-flex justify-content-center align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#markAttendanceModal">
                        <i class="ti ti-check"></i> Mark Attendance
                    </button>
                    <form method="GET" action="{{ route('dashboard.teacher.attendances.index') }}">
                        <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="today" {{ $filter == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="week" {{ $filter == 'week' ? 'selected' : '' }}>This Week</option>
                            <option value="month" {{ $filter == 'month' ? 'selected' : '' }}>This Month</option>
                            <option value="overall" {{ $filter == 'overall' ? 'selected' : '' }}>Overall</option>
                        </select>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @forelse ($classGroups as $group)
                    <h6 class="text-primary mb-3">{{ $group->name }} ({{ $group->subject->name ?? '' }})</h6>
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Parent</th>
                                <th>Check-In</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendanceRecords->where('class_group_id', $group->id) as $attendance)
                                <tr>
                                    <td>{{ $attendance->student->name }}</td>
                                    <td>
                                        @php
                                            $parent = \App\Models\ParentChild::where(
                                                'child_id',
                                                $attendance->student_id,
                                            )->first();
                                        @endphp
                                        {{ $parent?->parent?->name ?? '—' }}
                                    </td>
                                    <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('d M Y, h:i A') : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <hr>
                @empty
                    <p class="text-muted">No class groups assigned to you.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Mark Attendance Modal -->
    <div class="modal fade" id="markAttendanceModal" tabindex="-1" aria-labelledby="markAttendanceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('dashboard.teacher.attendances.store') }}" method="POST" id="markAttendanceForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="markAttendanceModalLabel">Mark Attendance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select Group</label>
                            <select name="class_group_id" id="groupSelect" class="form-select select2" required>
                                <option value="">-- Select Group --</option>
                                @foreach ($classGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}
                                        ({{ $group->subject->name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Student</label>
                            <select name="student_id[]" id="studentSelect" class="form-select select2" multiple required>
                                <option value="">-- Select Student --</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Check-In Date & Time</label>
                            <input type="datetime-local" name="check_in" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Attendance</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('script')
    {{-- <script src="{{asset('assets/js/app-user-list.js')}}"></script> --}}
    <script>
        $(document).ready(function() {

            // When group is selected, fetch its students
            $('#groupSelect').on('change', function() {
                const groupId = $(this).val();
                if (!groupId) {
                    $('#studentSelect').html('<option value="">-- Select Students --</option>');
                    return;
                }

                $.ajax({
                    url: '{{ route('dashboard.teacher.attendances.getStudents') }}',
                    type: 'GET',
                    data: {
                        group_id: groupId
                    },
                    success: function(response) {
                        let options = '<option value="">-- Select Students --</option>';
                        $.each(response.students, function(key, student) {
                            options +=
                                `<option value="${student.id}">${student.name}</option>`;
                        });
                        $('#studentSelect').html(options);
                    },
                    error: function() {
                        alert('Failed to fetch students. Please try again.');
                    }
                });
            });
        });
    </script>
@endsection
