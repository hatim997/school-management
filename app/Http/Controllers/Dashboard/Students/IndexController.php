<?php

namespace App\Http\Controllers\Dashboard\Students;

use App\Http\Controllers\Controller;
use App\Models\ChildSubject;
use App\Models\ClassGroup;
use App\Models\ClassGroupMaterial;
use App\Models\ClassGroupStudent;
use App\Models\ParentChild;
use App\Models\StudentAttendance;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
    public function enrolledSubjects()
    {
        try {
            $parentChild = ParentChild::where('child_id', auth()->user()->id)->firstOrFail();

            // Get all class groups that this child is enrolled in
            $classGroups = ClassGroupStudent::with([
                'classGroup.subject',
                'classGroup.teacher'
            ])
                ->where('parent_child_id', $parentChild->id)
                ->get()
                ->pluck('classGroup');

            // dd($classGroups);
            return view('dashboard.students.enrolled_subjects', compact('classGroups'));
        } catch (\Throwable $th) {
            Log::error('Students Enrolled Subjects Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }
    public function enrolledSubjectShow($id)
    {
        try {
            $classGroup = ClassGroup::with([
                'subject',
                'teacher.profile:id,user_id,profile_image,qualifications',
                'schedules',
            ])
            ->findOrFail($id);
            $classGroupMaterials = ClassGroupMaterial::with('user')->where('class_group_id', $classGroup->id)->get();
            return view('dashboard.students.enrolled_subject_details', compact('classGroup','classGroupMaterials'));
        } catch (\Throwable $th) {
            Log::error('Students Enrolled Subjects Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    public function markAttendance(Request $request)
    {
        $request->validate([
            'class_group_id' => 'required|integer',
        ]);

        $studentId = Auth::id();

        // Mark attendance only if not already done for today
        $studentAttendance = StudentAttendance::where('class_group_id', $request->class_group_id)
            ->where('student_id', $studentId)
            ->whereDate('check_in', Carbon::today())
            ->first();
        if ($studentAttendance) {
            return response()->json(['success' => true, 'message' => 'Attendance already marked for today!']);
        }
        $studentAttendance = new StudentAttendance();
        $studentAttendance->class_group_id = $request->class_group_id;
        $studentAttendance->student_id = $studentId;
        $studentAttendance->check_in = Carbon::now();
        $studentAttendance->save();

        return response()->json(['success' => true, 'message' => 'Attendance marked for today successfully!']);
    }

    public function upcomingClasses()
    {
        try {
            $studentId = Auth::id();
            $today = strtolower(Carbon::now()->format('l')); // e.g. monday, tuesday

            // Get the parent's record linked to this student
            $parentChild = ParentChild::where('child_id', $studentId)->firstOrFail();

            // Get class groups this student is enrolled in
            $classGroupIds = ClassGroupStudent::where('parent_child_id', $parentChild->id)
                ->pluck('class_group_id');

            // Fetch only today's classes with teacher & subject
            $todayClasses = ClassGroup::select('id', 'name', 'teacher_id', 'subject_id')
                ->with([
                    'subject:id,name',
                    'teacher:id,name',
                    'schedules' => function ($query) use ($today) {
                        $query->whereRaw('LOWER(day) = ?', [$today])
                            ->orderBy('start_time');
                    }
                ])
                ->whereIn('id', $classGroupIds)
                ->whereHas('schedules', function ($query) use ($today) {
                    $query->whereRaw('LOWER(day) = ?', [$today]);
                })
                ->get();

            return view('dashboard.students.upcoming_classes', compact('todayClasses'));
        } catch (\Throwable $th) {
            Log::error('Fetching Upcoming Classes Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
        }
    }

    public function studentCalendar()
    {
        try {
            $studentId = Auth::id();
            $parentChild = ParentChild::where('child_id', $studentId)->firstOrFail();

            // Get class groups this student is enrolled in
            $classGroupIds = ClassGroupStudent::where('parent_child_id', $parentChild->id)
                ->pluck('class_group_id');

            // Fetch all schedules for those class groups
            $classes = ClassGroup::with([
                'subject:id,name',
                'teacher:id,name',
                'schedules:id,class_group_id,day,start_time,end_time'
            ])->whereIn('id', $classGroupIds)->get();

            $events = [];

            // Convert weekly schedules into calendar events
            foreach ($classes as $class) {
                foreach ($class->schedules as $schedule) {
                    $dayOfWeek = strtolower($schedule->day);
                    $startTime = Carbon::parse($schedule->start_time);
                    $endTime = Carbon::parse($schedule->end_time);

                    // Generate events for upcoming 4 weeks
                    $startDate = Carbon::now()->startOfMonth();
                    $daysInMonth = Carbon::now()->daysInMonth;

                    for ($i = 0; $i < $daysInMonth; $i++) {
                        $date = $startDate->copy()->addDays($i);
                        if (strtolower($date->format('l')) === $dayOfWeek) {
                            $events[] = [
                                'group_name' => $class->name,
                                'start' => $date->format('Y-m-d') . 'T' . $startTime->format('H:i:s'),
                                'end' => $date->format('Y-m-d') . 'T' . $endTime->format('H:i:s'),
                                'subject' => $class->subject->name ?? 'N/A',
                                'teacher' => $class->teacher->name ?? 'N/A',
                                'day' => ucfirst($schedule->day),
                                'start_time' => $startTime->format('h:i A'),
                                'end_time' => $endTime->format('h:i A'),
                            ];
                        }
                    }
                }
            }

            // dd($events);

            return view('dashboard.students.calendar', compact('events'));
        } catch (\Throwable $th) {
            Log::error('Students Calendar Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
        }
    }
}
