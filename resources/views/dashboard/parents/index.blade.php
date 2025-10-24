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
                            <a href="{{ route('dashboard.children.index') }}" class="btn btn-primary">View
                                Children</a>
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
                                <h4 class="mb-0">{{ count($children) }}</h4>
                            </div>
                            <p class="mb-1">Total Children Enrolled</p>
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
                                <h4 class="mb-0">{{ $totalSubjects }}</h4>
                            </div>
                            <p class="mb-1">Total Courses Enrolled</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 mt-2">
                    <div class="card card-border-shadow-success h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="ti ti-coin icon-28px"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">{{ \App\Helpers\Helper::formatCurrency($totalPaid) }}</h4>
                            </div>
                            <p class="mb-1">Total Paid</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 mt-2">
                    <div class="card card-border-shadow-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="ti ti-coin icon-28px"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">{{ \App\Helpers\Helper::formatCurrency($pendingPayments) }}</h4>
                            </div>
                            <p class="mb-1">Pending Amount</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- View sales -->

<!-- Subject Progress -->
<div class="col-12 col-xl-6 col-md-6">
    <div class="card shadow-sm border-0 p-3">
        <h5>Subject Progress</h5>
        <canvas id="progressChart"></canvas>
    </div>
</div>

<!-- Payment Trends -->
<div class="col-12 col-xl-4 col-md-4">
    <div class="card shadow-sm border-0 p-3">
        <h5>Monthly Payments</h5>
        <canvas id="paymentChart"></canvas>
    </div>
</div>

<!-- Attendance -->
<div class="col-md-8">
    <div class="card shadow-sm border-0 p-3">
        <h5>Attendance % by Child</h5>
        <canvas id="attendanceChart"></canvas>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const paymentChart = new Chart(document.getElementById('paymentChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyPayments->keys()) !!},
                datasets: [{
                    label: 'Payments ($)',
                    data: {!! json_encode($monthlyPayments->values()) !!},
                    backgroundColor: '#4CAF50'
                }]
            }
        });

        const progressChart = new Chart(document.getElementById('progressChart'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($subjectProgress->keys()) !!},
                datasets: [{
                    data: {!! json_encode($subjectProgress->values()) !!},
                    backgroundColor: ['#2196F3', '#FFC107', '#8BC34A']
                }]
            }
        });

        const attendanceChart = new Chart(document.getElementById('attendanceChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($attendanceStats ? array_keys($attendanceStats) : []) !!},
                datasets: [{
                    label: 'Attendance %',
                    data: {!! json_encode($attendanceStats ? array_values($attendanceStats) : []) !!},
                    fill: false,
                    borderColor: '#FF5722',
                    tension: 0.3
                }]
            }
        });
    });
</script>
