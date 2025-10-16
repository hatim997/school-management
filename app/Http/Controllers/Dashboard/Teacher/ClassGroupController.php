<?php

namespace App\Http\Controllers\Dashboard\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassGroup;
use App\Models\ClassGroupMaterial;
use App\Models\ClassGroupSchedule;
use App\Models\ClassGroupStudent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClassGroupController extends Controller
{
    public function index()
    {
        $this->authorize('view class groups');
        try {
            $teacher = User::where('id', auth()->user()->id)->first();
            $classGroups = ClassGroup::with('teacher', 'subject', 'classGroupStudents')->where('teacher_id', $teacher->id)->get();
            // dd($teachers);
            return view('dashboard.teachers.class-groups.index', compact('classGroups'));
        } catch (\Throwable $th) {
            Log::error('Teachers Class Groups Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    public function show($id)
    {
        $this->authorize('view class groups');
        try {
            $classGroup = ClassGroup::with('teacher', 'subject')->findOrFail($id);
            $classGroupStudents = ClassGroupStudent::with([
                'parentChild.child:id,name',
                'parentChild.child.profile:id,user_id,profile_image',
                'parentChild.parent:id,name',
            ])->where('class_group_id', $classGroup->id)
                ->get();

            $classGroupSchedules = ClassGroupSchedule::with('classGroup')
                ->where('class_group_id', $classGroup->id)
                ->orderByRaw("FIELD(day, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
                ->get();

            // Transform data into a clean array
            $students = $classGroupStudents->map(function ($student) {
                return [
                    'child_name'     => $student->parentChild->child->name ?? 'N/A',
                    'child_image'    => asset($student->parentChild->child->profile->profile_image ?? 'assets/img/default/user.png'),
                    'parent_name'    => $student->parentChild->parent->name ?? 'N/A',
                ];
            });
            // dd($teachers);
            return view('dashboard.teachers.class-groups.show', compact('classGroup', 'students', 'classGroupSchedules'));
        } catch (\Throwable $th) {
            Log::error('Teachers Class Groups Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    public function classGroupMaterials($id)
    {
        $this->authorize('view class groups');
        try {
            $classGroup = ClassGroup::findOrFail($id);
            $classGroupMaterials = ClassGroupMaterial::where('class_group_id', $classGroup->id)->get();
            // dd($teachers);
            return view('dashboard.teachers.class-groups.materials', compact('classGroupMaterials', 'classGroup'));
        } catch (\Throwable $th) {
            Log::error('Teachers Class Groups Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    public function storeClassGroupMaterials(Request $request, $id)
    {
        $this->authorize('view class groups');
        $validator = Validator::make($request->all(), [
            'file_name' => 'required|string|max:255',
            'file' => 'required|file|max_size',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'Validation Error!');
        }
        try {
            $classGroup = ClassGroup::findOrFail($id);
            $classGroupMaterial = new ClassGroupMaterial();
            $classGroupMaterial->user_id = auth()->user()->id;
            $classGroupMaterial->class_group_id = $classGroup->id;
            $classGroupMaterial->file_name = $request->file_name;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $file_ext = $file->getClientOriginalExtension();
                $mime_type = $file->getClientMimeType();
                $file_size = $file->getSize();

                $file_name = time() . '_file.' . $file_ext;
                $file_path = 'uploads/subject-materials';
                $file->move(public_path($file_path), $file_name);

                $classGroupMaterial->file = $file_path . "/" . $file_name;
                $classGroupMaterial->file_type = $mime_type;
                $classGroupMaterial->file_size = $file_size;
            }

            $classGroupMaterial->save();

            // dd($teachers);
            return redirect()->route('dashboard.class-groups.materials', $classGroup->id)->with('success', 'Material Uploaded Successfully!');
        } catch (\Throwable $th) {
            Log::error('Materials Upload Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    public function upcomingSessions()
    {
        try {
            $teacherId = Auth::id();
            $today = strtolower(Carbon::now()->format('l')); // e.g. monday, tuesday

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
                ->where('teacher_id', $teacherId)
                ->whereHas('schedules', function ($query) use ($today) {
                    $query->whereRaw('LOWER(day) = ?', [$today]);
                })
                ->get();

            return view('dashboard.teachers.class-groups.upcoming_sessions', compact('todayClasses'));
        } catch (\Throwable $th) {
            Log::error('Fetching Upcoming Classes Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
        }
    }

    public function teacherCalendar()
    {
        try {
            $teacherId = Auth::id();
            // Fetch all schedules for those class groups
            $classes = ClassGroup::with([
                'subject:id,name',
                'teacher:id,name',
                'schedules:id,class_group_id,day,start_time,end_time'
            ])->where('teacher_id', $teacherId)->get();

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

            return view('dashboard.teachers.class-groups.calendar', compact('events'));
        } catch (\Throwable $th) {
            Log::error('Students Calendar Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
        }
    }
}
