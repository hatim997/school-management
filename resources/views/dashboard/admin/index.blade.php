<!-- View sales -->
<div class="col-xl-6">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-7">
                        <div class="card-body text-nowrap">
                            <h5 class="card-title mb-0">Welcome {{ Auth::user()->name }}! 🎉</h5>
                            <p class="mb-2">Here what's happening in your account today</p>
                            <a href="{{ route('profile.index') }}" class="btn btn-primary">View Profile</a>
                        </div>
                    </div>
                    <div class="col-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/card-advance-sale.png') }}" height="140"
                                alt="view sales" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12">
            <div class="row">
                <!-- Total Parents -->
                <div class="col-lg-6 col-sm-6 mt-2">
                    <div class="card card-border-shadow-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="ti ti-users icon-28px"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">{{ \App\Helpers\Helper::formatCurrency($stats['revenue']) }}</h4>
                            </div>
                            <p class="mb-1">Total Revenue</p>
                        </div>
                    </div>
                </div>

                <!-- Total Enrolled Children -->
                <div class="col-lg-6 col-sm-6 mt-2">
                    <div class="card card-border-shadow-warning h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="ti ti-school icon-28px"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">{{ $stats['students'] }}</h4>
                            </div>
                            <p class="mb-1">Total Students</p>
                        </div>
                    </div>
                </div>

                <!-- Total Teachers -->
                <div class="col-lg-6 col-sm-6 mt-2">
                    <div class="card card-border-shadow-danger h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-danger">
                                        <i class="ti ti-chalkboard icon-28px"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">{{ $stats['teachers'] }}</h4>
                            </div>
                            <p class="mb-1">Total Teachers</p>
                        </div>
                    </div>
                </div>

                <!-- Total Groups -->
                <div class="col-lg-6 col-sm-6 mt-2">
                    <div class="card card-border-shadow-info h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-info">
                                        <i class="ti ti-users-group icon-28px"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">{{ $stats['groups'] }}</h4>
                            </div>
                            <p class="mb-1">Total Groups</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- View sales -->
<div class="col-12 col-xl-6 col-md-6">
    <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title mb-0">
                <h5 class="m-0 me-2">Popular Instructors</h5>
            </div>
        </div>
        <div class="px-5 py-4 border border-start-0 border-end-0">
            <div class="d-flex justify-content-between align-items-center">
                <p class="mb-0 text-uppercase">Teachers</p>
                <p class="mb-0 text-uppercase">Assigned Students</p>
            </div>
        </div>
        <div class="card-body">
            @if (isset($popularTeachers) && count($popularTeachers) > 0)
                @foreach ($popularTeachers as $teacher)
                    <div class="d-flex justify-content-between align-items-center mb-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar me-4">
                                <img src="{{ asset($teacher->profile->profile_image ?? 'assets/img/default/user.png') }}" alt="Avatar" class="rounded-circle" />
                            </div>
                            <div>
                                <div>
                                    <h6 class="mb-0 text-truncate">{{ $teacher->name }}</h6>
                                    <small class="text-truncate text-body">{{ $teacher->profile->qualifications }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <h6 class="mb-0">{{ $teacher->total_students }}</h6>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-4">
                    <i class="ti ti-users text-muted" style="font-size: 48px;"></i>
                    <p class="mt-2 text-muted">No Teacher Available</p>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="col-12 col-xl-12">
    <div class="row">
        <div class="col-md-6">
            <div class="card p-3">
                <h6>Enrollments per Subject</h6>
                <div id="subjectEnrollChart"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3">
                <h6>Monthly Revenue</h6>
                <div id="revenueChart"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const subjectEnrollChart = new ApexCharts(document.querySelector("#subjectEnrollChart"), {
        chart: { type: 'bar', height: 300 },
        series: [{ name: 'Enrollments', data: @json($charts['subject_enrollments']['data']) }],
        xaxis: { categories: @json($charts['subject_enrollments']['labels']) }
    });
    subjectEnrollChart.render();

    const revenueChart = new ApexCharts(document.querySelector("#revenueChart"), {
        chart: { type: 'line', height: 300 },
        series: [{ name: 'Revenue', data: @json($charts['revenue']['data']) }],
        xaxis: { categories: @json($charts['revenue']['months']) }
    });
    revenueChart.render();
});
</script>

