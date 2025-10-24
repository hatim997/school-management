<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ChildSubject;
use App\Models\ClassGroup;
use App\Models\ClassGroupMaterial;
use App\Models\ClassGroupSchedule;
use App\Models\ClassGroupStudent;
use App\Models\Notification;
use App\Models\ParentChild;
use App\Models\Payment;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = User::findOrFail(Auth::user()->id);
            $unreadNotificationsCount = Notification::where('user_id', $user->id)
                ->whereNull('read_at')
                ->count();

            $todayClass = null;
            $now = Carbon::now();
            $today = strtolower($now->format('l'));

            if ($user->hasRole(['admin', 'super-admin'])) {
                $stats = [
                    'students' => User::role('student')->count(),
                    'teachers' => User::role('teacher')->count(),
                    'subjects' => Subject::count(),
                    'groups'   => ClassGroup::count(),
                    'revenue'  => Payment::where('payment_status', 'success')->sum('amount'),
                ];

                $charts = [
                    'subject_enrollments' => [
                        'labels' => Subject::pluck('name'),
                        'data' => Subject::withCount('childSubjects')->pluck('child_subjects_count')
                    ],
                    'revenue' => [
                        'months' => collect(range(1, 12))->map(fn($m) => Carbon::create()->month($m)->format('M')),
                        'data' => collect(range(1, 12))->map(
                            fn($m) =>
                            Payment::whereMonth('created_at', $m)
                                ->where('payment_status', 'success')
                                ->sum('amount')
                        ),
                    ],
                ];

                $popularTeachers = User::with('profile:id,user_id,profile_image,qualifications')->role('teacher')
                    ->select('users.id', 'users.name',  DB::raw('COUNT(DISTINCT parent_children.child_id) as total_students'))
                    ->leftJoin('class_groups', 'class_groups.teacher_id', '=', 'users.id')
                    ->leftJoin('class_group_students', 'class_group_students.class_group_id', '=', 'class_groups.id')
                    ->leftJoin('parent_children', 'parent_children.id', '=', 'class_group_students.parent_child_id')
                    ->groupBy('users.id', 'users.name')
                    ->orderByDesc('total_students')
                    ->take(5)
                    ->get();

                // dd($popularTeachers);

                return view('dashboard.index', compact('unreadNotificationsCount', 'stats', 'charts', 'popularTeachers'));
            }

            if ($user->hasRole('student')) {
                $parentChild = ParentChild::where('child_id', $user->id)->first();

                if ($parentChild) {
                    $classGroupIds = ClassGroupStudent::where('parent_child_id', $parentChild->id)
                        ->pluck('class_group_id');

                    $todayClass = ClassGroup::select('id', 'name', 'teacher_id', 'subject_id')
                        ->with([
                            'subject:id,name',
                            'teacher:id,name',
                            'schedules' => function ($query) use ($today, $now) {
                                $query->whereRaw('LOWER(day) = ?', [$today])
                                    ->where(function ($q) use ($now) {
                                        $q->where('start_time', '>', $now->format('H:i:s')) // upcoming
                                            ->orWhere(function ($inner) use ($now) {
                                                $inner->where('start_time', '<=', $now->format('H:i:s'))
                                                    ->where('end_time', '>=', $now->format('H:i:s')); // current
                                            });
                                    })
                                    ->orderBy('start_time', 'asc');
                            }
                        ])
                        ->whereIn('id', $classGroupIds)
                        ->whereHas('schedules', function ($query) use ($today, $now) {
                            $query->whereRaw('LOWER(day) = ?', [$today])
                                ->where(function ($q) use ($now) {
                                    $q->where('start_time', '>', $now->format('H:i:s'))
                                        ->orWhere(function ($inner) use ($now) {
                                            $inner->where('start_time', '<=', $now->format('H:i:s'))
                                                ->where('end_time', '>=', $now->format('H:i:s'));
                                        });
                                });
                        })
                        ->orderBy(
                            ClassGroupSchedule::select('start_time')
                                ->whereColumn('class_group_id', 'class_groups.id')
                                ->whereRaw('LOWER(day) = ?', [$today])
                                ->orderBy('start_time', 'asc')
                                ->limit(1)
                        )
                        ->first();

                    // ✅ Dashboard Stats
                    $stats = [
                        'total_subjects' => ChildSubject::where('parent_child_id', $parentChild->id)->count(),
                        'completed_subjects' => ChildSubject::where('parent_child_id', $parentChild->id)->where('status', 'complete')->count(),
                        'in_progress_subjects' => ChildSubject::where('parent_child_id', $parentChild->id)->where('status', 'inprogress')->count(),
                        'total_classes' => $classGroupIds->count(),
                        'total_attendance' => StudentAttendance::where('student_id', $user->id)->count(),
                        'payments_total' => Payment::where('parent_child_id', $parentChild->id)->where('payment_status', 'success')->sum('amount'),
                    ];

                    // ✅ Charts Data
                    $charts = [
                        'attendance_trend' => StudentAttendance::where('student_id', $user->id)
                            ->select(
                                DB::raw('DATE(check_in) as date'),
                                DB::raw('COUNT(id) as total')
                            )
                            ->groupBy('date')
                            ->orderBy('date', 'desc')
                            ->limit(7)
                            ->get(),

                        'subject_progress' => ChildSubject::where('parent_child_id', $parentChild->id)
                            ->select('status', DB::raw('COUNT(id) as total'))
                            ->groupBy('status')
                            ->get(),

                        'payments_summary' => Payment::where('parent_child_id', $parentChild->id)
                            ->select('payment_method', DB::raw('SUM(amount) as total'))
                            ->where('payment_status', 'success')
                            ->groupBy('payment_method')
                            ->get(),
                    ];

                    // ✅ Alerts
                    $alerts = [];
                    if ($todayClass) {
                        $alerts[] = [
                            'type' => 'info',
                            'title' => 'Today’s Class',
                            'message' => 'Your class "' . $todayClass->name . '" starts soon.',
                        ];
                    }
                    if ($stats['in_progress_subjects'] > 0) {
                        $alerts[] = [
                            'type' => 'warning',
                            'title' => 'Incomplete Subjects',
                            'message' => 'You have ' . $stats['in_progress_subjects'] . ' subjects still in progress.',
                        ];
                    }

                    // ✅ Recent Materials & Attendance
                    $recentMaterials = ClassGroupMaterial::whereIn('class_group_id', $classGroupIds)
                        ->with('classGroup.subject:id,name')
                        ->latest()
                        ->limit(5)
                        ->get();

                    $recentAttendance = StudentAttendance::where('student_id', $user->id)
                        ->with('classGroup.subject:id,name')
                        ->orderBy('check_in', 'desc')
                        ->limit(5)
                        ->get();
                }

                return view('dashboard.index', compact(
                    'todayClass',
                    'unreadNotificationsCount',
                    'stats',
                    'charts',
                    'alerts',
                    'recentMaterials',
                    'recentAttendance'
                ));
            }

            if ($user->hasRole('teacher')) {
                // Teacher
                $todayClass = ClassGroup::select('id', 'name', 'teacher_id', 'subject_id')
                    ->with([
                        'subject:id,name',
                        'teacher:id,name',
                        'schedules' => function ($query) use ($today, $now) {
                            $query->whereRaw('LOWER(day) = ?', [$today])
                                ->where(function ($q) use ($now) {
                                    $q->where('start_time', '>', $now->format('H:i:s'))
                                        ->orWhere(function ($inner) use ($now) {
                                            $inner->where('start_time', '<=', $now->format('H:i:s'))
                                                ->where('end_time', '>=', $now->format('H:i:s'));
                                        });
                                })
                                ->orderBy('start_time', 'asc');
                        }
                    ])
                    ->where('teacher_id', $user->id)
                    ->whereHas('schedules', function ($query) use ($today, $now) {
                        $query->whereRaw('LOWER(day) = ?', [$today])
                            ->where(function ($q) use ($now) {
                                $q->where('start_time', '>', $now->format('H:i:s'))
                                    ->orWhere(function ($inner) use ($now) {
                                        $inner->where('start_time', '<=', $now->format('H:i:s'))
                                            ->where('end_time', '>=', $now->format('H:i:s'));
                                    });
                            });
                    })
                    ->orderBy(
                        ClassGroupSchedule::select('start_time')
                            ->whereColumn('class_group_id', 'class_groups.id')
                            ->whereRaw('LOWER(day) = ?', [$today])
                            ->orderBy('start_time', 'asc')
                            ->limit(1)
                    )
                    ->first();

                // 🔹 Basic Stats for Teacher
                $stats = [
                    'total_classes' => ClassGroup::where('teacher_id', $user->id)->count(),
                    'total_students' => DB::table('class_groups')
                        ->join('class_group_students', 'class_groups.id', '=', 'class_group_students.class_group_id')
                        ->join('parent_children', 'parent_children.id', '=', 'class_group_students.parent_child_id')
                        ->where('class_groups.teacher_id', $user->id)
                        ->distinct('parent_children.child_id')
                        ->count('parent_children.child_id'),
                    'active_subjects' => Subject::whereIn(
                        'id',
                        ClassGroup::where('teacher_id', $user->id)->pluck('subject_id')
                    )->count(),
                    'active_groups' => ClassGroup::where('teacher_id', $user->id)
                        ->where('is_active', 'active')->count(),
                ];

                // 🔹 Charts / Graphs Data
                $charts = [
                    // 📊 Students per class group
                    'students_per_class' => DB::table('class_groups as cg')
                        ->leftJoin('class_group_students as cgs', 'cg.id', '=', 'cgs.class_group_id')
                        ->leftJoin('parent_children as pc', 'pc.id', '=', 'cgs.parent_child_id')
                        ->where('cg.teacher_id', $user->id)
                        ->select('cg.name', DB::raw('COUNT(DISTINCT pc.child_id) as total'))
                        ->groupBy('cg.id', 'cg.name')
                        ->get(),

                    // 📈 Class capacity utilization (%)
                    'class_capacity' => DB::table('class_groups as cg')
                        ->leftJoin('class_group_students as cgs', 'cg.id', '=', 'cgs.class_group_id')
                        ->leftJoin('parent_children as pc', 'pc.id', '=', 'cgs.parent_child_id')
                        ->where('cg.teacher_id', $user->id)
                        ->select(
                            'cg.name',
                            'cg.capacity',
                            DB::raw('COUNT(DISTINCT pc.child_id) as total_students')
                        )
                        ->groupBy('cg.id', 'cg.name', 'cg.capacity')
                        ->get()
                        ->map(function ($g) {
                            $g->utilization = round(($g->total_students / max($g->capacity, 1)) * 100, 1);
                            return $g;
                        }),

                    // 🕓 Classes scheduled per weekday
                    'weekly_schedule' => collect(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])
                        ->map(function ($day) use ($user) {
                            $count = \App\Models\ClassGroupSchedule::whereHas(
                                'classGroup',
                                fn($q) => $q->where('teacher_id', $user->id)
                            )->whereRaw('LOWER(day) = ?', [$day])->count();
                            return ['day' => ucfirst($day), 'count' => $count];
                        }),
                ];

                // dd($stats);

                return view('dashboard.index', compact('todayClass', 'unreadNotificationsCount', 'stats', 'charts'));
            }
            
            if ($user->hasRole('parent')) {
                // Get all children of parent
                $children = ParentChild::where('parent_id', $user->id)->pluck('child_id');
                $childRelations = ParentChild::where('parent_id', $user->id)->get();

                // Subjects enrolled by children
                $enrolledSubjects = ChildSubject::whereIn('parent_child_id', $childRelations->pluck('id'))
                    ->with('subject:id,name,price')
                    ->get();

                // Payments stats
                $payments = Payment::whereIn('parent_child_id', $childRelations->pluck('id'))->get();
                $totalPaid = $payments->where('payment_status', 'success')->sum('amount');
                $pendingPayments = $payments->where('payment_status', 'pending')->sum('amount');
                $failedPayments = $payments->where('payment_status', 'failed')->sum('amount');

                // Attendance percentage for each child
                $attendanceStats = [];
                foreach ($children as $childId) {
                    $totalClasses = ClassGroupStudent::whereIn('parent_child_id', $childRelations->pluck('id'))
                        ->count();
                    $attended = StudentAttendance::where('student_id', $childId)->count();
                    $attendanceStats[$childId] = $totalClasses > 0 ? round(($attended / $totalClasses) * 100, 2) : 0;
                }

                // Subjects performance (status wise)
                $subjectProgress = ChildSubject::whereIn('parent_child_id', $childRelations->pluck('id'))
                    ->selectRaw("status, COUNT(*) as total")
                    ->groupBy('status')
                    ->pluck('total', 'status');

                // Monthly payment chart data
                $monthlyPayments = Payment::whereIn('parent_child_id', $childRelations->pluck('id'))
                    ->selectRaw("DATE_FORMAT(created_at, '%b') as month, SUM(amount) as total")
                    ->where('payment_status', 'success')
                    ->groupBy('month')
                    ->pluck('total', 'month');

                // Total enrolled subjects
                $totalSubjects = $enrolledSubjects->count();

                // Active classes (current day)
                $today = strtolower(now()->format('l'));
                $now = now()->format('H:i:s');

                $todayClasses = ClassGroup::whereHas('schedules', function ($q) use ($today, $now) {
                    $q->whereRaw('LOWER(day) = ?', [$today])
                        ->where('start_time', '<=', $now)
                        ->where('end_time', '>=', $now);
                })
                    ->whereIn('id', ClassGroupStudent::whereIn('parent_child_id', $childRelations->pluck('id'))
                        ->pluck('class_group_id'))
                    ->with(['subject:id,name', 'teacher:id,name'])
                    ->get();

                return view('dashboard.index', compact(
                    'children',
                    'totalPaid',
                    'pendingPayments',
                    'failedPayments',
                    'attendanceStats',
                    'subjectProgress',
                    'monthlyPayments',
                    'totalSubjects',
                    'todayClasses'
                ));
            }


            return view('dashboard.index', compact('todayClass', 'unreadNotificationsCount'));
        } catch (\Throwable $th) {
            Log::error("Dashboard Index Failed:" . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again later');
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
