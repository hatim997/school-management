<?php

namespace App\Http\Controllers\Dashboard\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassGroup;
use App\Models\ClassGroupStudent;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            // 1️⃣ Get all class groups where this teacher is assigned
            $classGroups = ClassGroup::where('teacher_id', $user->id)
                ->with(['classGroupStudents.parentChild.child'])
                ->get();

            // 2️⃣ Handle filter type (today, week, month, overall)
            $filter = $request->get('filter', 'today'); // default = today

            $attendanceQuery = StudentAttendance::whereIn('class_group_id', $classGroups->pluck('id'))
                ->with(['student', 'classGroup'])
                ->orderBy('check_in', 'desc');

            // 3️⃣ Apply filter
            switch ($filter) {
                case 'week':
                    $attendanceQuery->whereBetween('check_in', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek(),
                    ]);
                    break;

                case 'month':
                    $attendanceQuery->whereBetween('check_in', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;

                case 'overall':
                    // no filter applied
                    break;

                default: // today
                    $attendanceQuery->whereDate('check_in', Carbon::today());
                    break;
            }

            $attendanceRecords = $attendanceQuery->get();

            return view('dashboard.teachers.attendance.index', compact('classGroups', 'attendanceRecords', 'filter'));
        } catch (\Throwable $th) {
            Log::error('Attendance Index Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
        }
    }

    public function getStudents(Request $request)
    {
        $groupId = $request->group_id;

        if (!$groupId) {
            return response()->json(['students' => []]);
        }

        // Get all students (children) linked to this class group
        $students = ClassGroupStudent::with(['parentChild.child'])
            ->where('class_group_id', $groupId)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->parentChild->child->id ?? null,
                    'name' => $item->parentChild->child->name ?? 'Unknown',
                ];
            })
            ->filter(fn($s) => $s['id'] !== null)
            ->values();

        return response()->json([
            'students' => $students
        ]);
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
        $this->authorize('create attendance');
        $validator = Validator::make($request->all(), [
            'class_group_id' => 'required|exists:class_groups,id',
            'student_id' => 'required|array|min:1',
            'student_id.*' => 'exists:users,id',
            'check_in' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'Validation Error!');
        }
        try {
            $classGroupId = $request->class_group_id;
            $checkIn = $request->check_in;

            foreach ($request->student_id as $studentId) {
                // Prevent duplicate attendance for same date
                $alreadyMarked = StudentAttendance::where('class_group_id', $classGroupId)
                    ->where('student_id', $studentId)
                    ->whereDate('check_in', Carbon::parse($checkIn)->toDateString())
                    ->exists();

                if (!$alreadyMarked) {
                    $studentAttendance = new StudentAttendance();
                    $studentAttendance->class_group_id = $classGroupId;
                    $studentAttendance->student_id = $studentId;
                    $studentAttendance->check_in = $checkIn;
                    $studentAttendance->save();
                }
            }

            // dd($teachers);
            return redirect()->route('dashboard.teacher.attendances.index')->with('success', 'Attendance Marked Successfully!');
        } catch (\Throwable $th) {
            Log::error('Mark Attendance Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
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
