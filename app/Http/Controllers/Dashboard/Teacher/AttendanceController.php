<?php

namespace App\Http\Controllers\Dashboard\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassGroup;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
