@extends('layouts.master')

@section('title', __('Subjects'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item active">{{ __('Subjects') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="app-academy">
            <div class="card p-0 mb-6">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between p-0 pt-6">
                    <div class="app-academy-md-25 card-body py-0 pt-6 ps-12">
                        <img src="{{ asset('assets/img/illustrations/bulb-light.png') }}"
                            class="img-fluid app-academy-img-height scaleX-n1-rtl" alt="Bulb in hand"
                            data-app-light-img="illustrations/bulb-light.png" data-app-dark-img="illustrations/bulb-dark.png"
                            height="90" />
                    </div>
                    <div
                        class="app-academy-md-50 card-body d-flex align-items-md-center flex-column text-md-center mb-6 py-6">
                        <span class="card-title mb-4 lh-lg px-md-12 h4 text-heading">
                            Education, talents, and career<br />
                            opportunities. <span class="text-primary text-nowrap">All in one place</span>.
                        </span>
                        <p class="mb-4">
                            Grow your skill with the most reliable online subjects and certifications in<br />
                            marketing, information technology, programming, and data science.
                        </p>
                        <form action="{{ route('dashboard.subjects.index') }}" method="GET"
                            class="d-flex align-items-center justify-content-between app-academy-md-80">
                            <input type="search" name="search" placeholder="Find your course" class="form-control me-4" />
                            <button type="submit" class="btn btn-primary btn-icon">
                                <i class="icon-base ti ti-search icon-22px"></i>
                            </button>
                        </form>
                    </div>
                    <div class="app-academy-md-25 d-flex align-items-end justify-content-end">
                        <img src="{{ asset('assets/img/illustrations/pencil-rocket.png') }}" alt="pencil rocket"
                            height="188" class="scaleX-n1-rtl" />
                    </div>
                </div>
            </div>

            <div class="card mb-6">
                <div class="card-header d-flex flex-wrap justify-content-between gap-4">
                    <div class="card-title mb-0 me-1">
                        <h5 class="mb-0">Subjects</h5>
                        <p class="mb-0">Total {{ $totalSubjects }} subjects</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row gy-6 mb-6">
                        @if (isset($subjects) && count($subjects) > 0)
                            @foreach ($subjects as $name => $group)
                                @php
                                    // Extract ages (e.g., "5-8", "9-15")
                                    $ages = $group
                                        ->map(function ($sub) {
                                            return "{$sub->from_age}-{$sub->to_age}";
                                        })
                                        ->unique()
                                        ->values()
                                        ->implode(' & ');

                                    // Use the first item for image, description, price, etc.
                                    $subject = $group->first();

                                    // Determine if all versions are "coming soon"
                                    $isComing = $group->every(fn($sub) => $sub->is_coming == '1');
                                @endphp

                                <div class="col-sm-6 col-lg-4">
                                    <div class="card p-2 h-100 shadow-none border">
                                        <div class="rounded-2 text-center mb-4">
                                            <a href="javascript:void(0)">
                                                <img class="img-fluid"
                                                    src="{{ $subject->image ? asset($subject->image) : asset('uploads/subjects/subject-default-image.jpg') }}"
                                                    alt="{{ $name }}" />
                                            </a>
                                        </div>
                                        <div class="card-body p-4 pt-2">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <span class="badge bg-label-primary">{{ $subject->code }}</span>
                                                <p
                                                    class="d-flex align-items-center justify-content-center fw-medium gap-1 mb-0">
                                                    {{ $subject->rating }}
                                                    <span class="text-warning">
                                                        <i class="icon-base ti ti-star-filled icon-lg me-1 mb-1_5"></i>
                                                    </span>
                                                    <span class="fw-normal">({{ $subject->total_enrolled }})</span>
                                                </p>
                                            </div>

                                            <h5 class="h5 mb-2">{{ $name }}</h5>
                                            {{-- <p class="mt-1">{{ $subject->short_description }}</p> --}}
                                            <p class="text-muted mb-2">Class Ages: {{ $ages }} Years</p>
                                            <p class="d-flex align-items-center mb-1">
                                                <i class="icon-base ti ti-clock me-1"></i>{{ $subject->duration }} weeks
                                            </p>

                                            <div
                                                class="d-flex flex-column flex-md-row gap-4 text-nowrap flex-wrap flex-md-nowrap flex-lg-wrap flex-xxl-nowrap">
                                                @if ($isComing)
                                                    <button
                                                        class="w-100 btn btn-secondary d-flex align-items-center justify-content-center"
                                                        disabled>
                                                        <span class="me-2">Coming Soon</span>
                                                        <i class="icon-base ti ti-clock-hour-4 icon-xs lh-1"></i>
                                                    </button>
                                                @else
                                                    <a class="w-100 btn btn-primary d-flex align-items-center"
                                                        href="{{ route('dashboard.checkout.index', $subject->id) }}">
                                                        <span class="me-2">
                                                            Purchase Now
                                                            ({{ \App\Helpers\Helper::formatCurrency($subject->price) }})
                                                        </span>
                                                        <i
                                                            class="icon-base ti tabler-chevron-right icon-xs lh-1 scaleX-n1-rtl"></i>
                                                    </a>
                                                @endif
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
