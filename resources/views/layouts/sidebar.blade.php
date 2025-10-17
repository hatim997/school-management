<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo">
                <img height="40px" src="{{ asset(\App\Helpers\Helper::getLogoLight()) }}" alt="{{ env('APP_NAME') }}">
            </span>
            <span class="app-brand-text menu-text fw-bold">{{ \App\Helpers\Helper::getCompanyName() }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    @php
        use Illuminate\Support\Str;
        $roleTitle = Str::title(str_replace('-', ' ', Auth::user()->getRoleNames()->first()));
    @endphp

    <div class="text-center mt-3 mb-2 px-3">
        <div class="border rounded-pill py-2 px-3 bg-label-primary text-primary fw-semibold shadow-sm small">
            <i class="ti ti-user-shield me-1"></i>
            {{ $roleTitle }} Portal
        </div>
    </div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div>{{ __('Dashboard') }}</div>
            </a>
        </li>

        <!-- Apps & Pages -->
        <li class="menu-header small">
            <span class="menu-header-text">{{ __('Apps & Pages') }}</span>
        </li>
        @role('parent')
            @can('view children')
                <li class="menu-item {{ request()->routeIs('dashboard.children.*') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.children.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-school"></i>
                        <div>{{ __('Children') }}</div>
                    </a>
                </li>
            @endcan

            @can('view subject')
                <li class="menu-item {{ request()->routeIs('dashboard.subjects.*') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.subjects.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-book"></i>
                        <div>{{ __('Subjects') }}</div>
                    </a>
                </li>
            @endcan
        @else
            @can('view subject')
                <li class="menu-item {{ request()->routeIs('dashboard.admin.subjects.*') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.admin.subjects.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-book"></i>
                        <div>{{ __('Subjects') }}</div>
                    </a>
                </li>
            @endcan
        @endrole
        @role('teacher')
            @can(['view class groups'])
                <li class="menu-item {{ request()->routeIs('dashboard.class-groups.*') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.class-groups.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-users-group"></i>
                        <div>{{ __('Class Groups') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('dashboard.teachers.upcoming-sessions') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.teachers.upcoming-sessions') }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-calendar-clock"></i>
                        <div>{{ __('Upcoming Sessions') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('dashboard.teachers.calendar') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.teachers.calendar') }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-calendar-event"></i>
                        <div>{{ __('Calendar') }}</div>
                    </a>
                </li>
            @endcan
            {{-- @can(['view attendance'])
                <li class="menu-item {{ request()->routeIs('dashboard.teacher.attendances.*') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.teacher.attendances.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-clock-check"></i>
                        <div>{{ __('Attendance') }}</div>
                    </a>
                </li>
            @endcan --}}
        @else
            @can(['view class groups'])
                <li class="menu-item {{ request()->routeIs('dashboard.admin-class-groups.*') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.admin-class-groups.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-users-group"></i>
                        <div>{{ __('Class Groups') }}</div>
                    </a>
                </li>
            @endcan
        @endrole
        @role('student')
            <li class="menu-item {{ request()->routeIs('dashboard.students.enrolled-subjects.*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.students.enrolled-subjects.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-book"></i>
                    <div>{{ __('Enrolled Subjects') }}</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('dashboard.students.upcoming-classes.*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.students.upcoming-classes.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-calendar-clock"></i>
                    <div>{{ __('Upcoming Classes') }}</div>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('dashboard.students.calendar') ? 'active' : '' }}">
                <a href="{{ route('dashboard.students.calendar') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-calendar-event"></i>
                    <div>{{ __('Calendar') }}</div>
                </a>
            </li>
        @endrole
        @can(['view teacher'])
            <li class="menu-item {{ request()->routeIs('dashboard.teachers.*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.teachers.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-id"></i>
                    <div>{{ __('Teachers') }}</div>
                </a>
            </li>
        @endcan
        @canany(['view user', 'view archived user'])
            <li
                class="menu-item {{ request()->routeIs('dashboard.user.*') || request()->routeIs('dashboard.archived-user.*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-users"></i>
                    <div>{{ __('Users') }}</div>
                </a>
                <ul class="menu-sub">
                    @can(['view user'])
                        <li class="menu-item {{ request()->routeIs('dashboard.user.*') ? 'active' : '' }}">
                            <a href="{{ route('dashboard.user.index') }}" class="menu-link">
                                <div>{{ __('All Users') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can(['view archived user'])
                        <li class="menu-item {{ request()->routeIs('dashboard.archived-user.*') ? 'active' : '' }}">
                            <a href="{{ route('dashboard.archived-user.index') }}" class="menu-link">
                                <div>{{ __('Archived Users') }}</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @canany(['view role', 'view permission'])
            <li
                class="menu-item {{ request()->routeIs('dashboard.roles.*') || request()->routeIs('dashboard.permissions.*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    {{-- <i class="menu-icon tf-icons ti ti-settings"></i> --}}
                    <i class="menu-icon tf-icons ti ti-shield-lock"></i>
                    <div>{{ __('Roles & Permissions') }}</div>
                </a>
                <ul class="menu-sub">
                    @can(['view role'])
                        <li class="menu-item {{ request()->routeIs('dashboard.roles.*') ? 'active' : '' }}">
                            <a href="{{ route('dashboard.roles.index') }}" class="menu-link">
                                <div>{{ __('Roles') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can(['view permission'])
                        <li class="menu-item {{ request()->routeIs('dashboard.permissions.*') ? 'active' : '' }}">
                            <a href="{{ route('dashboard.permissions.index') }}" class="menu-link">
                                <div>{{ __('Permissions') }}</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can(['view setting'])
            <li class="menu-item {{ request()->routeIs('dashboard.setting.*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.setting.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-settings"></i>
                    <div>{{ __('Settings') }}</div>
                </a>
            </li>
        @endcan
    </ul>
</aside>
