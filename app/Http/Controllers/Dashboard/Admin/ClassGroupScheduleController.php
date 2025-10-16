<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassGroupSchedule;
use App\Models\ClassGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClassGroupScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $this->authorize('view class group schedules');
        try {
            $classGroup = ClassGroup::findOrFail($id);
            $classGroupSchedules = ClassGroupSchedule::with('classGroup')
                ->where('class_group_id', $classGroup->id)
                ->orderByRaw("FIELD(day, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
                ->get();
            return view('dashboard.admin.class-groups.schedules.index',compact('classGroupSchedules','classGroup'));
        } catch (\Throwable $th) {
            Log::error('Class Group Schedule Index Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $this->authorize('create class group schedules');
        try {
            $classGroup = ClassGroup::findOrFail($id);
            return view('dashboard.admin.class-groups.schedules.create',compact('classGroup'));
        } catch (\Throwable $th) {
            Log::error('Class Group Schedule Create Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $this->authorize('create class group schedules');
        $validator = Validator::make($request->all(), [
            'day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'zoom_link' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'Validation Error!');
        }

        try {
            DB::beginTransaction();
            $classGroupschedule = ClassGroupSchedule::where('class_group_id', $id)->where('day', $request->day)->first();
            // dd($classGroupschedule);
            if (!$classGroupschedule) {
                $classGroupschedule = new ClassGroupSchedule();
                $classGroupschedule->class_group_id = $id;
                $classGroupschedule->day = $request->day;
            }
            $classGroupschedule->start_time = $request->start_time;
            $classGroupschedule->end_time = $request->end_time;
            $classGroupschedule->zoom_link = $request->zoom_link;
            $classGroupschedule->save();

            DB::commit();
            return redirect()->route('dashboard.class-group-schedules.index', $id)->with('success', 'Schedule Created Successfully');
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
            Log::error('Schedule Created Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
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
        $this->authorize('update class group schedules');
        try {
            $classGroupschedule = ClassGroupSchedule::with('classGroup')->findOrFail($id);
            return view('dashboard.admin.class-groups.schedules.edit', compact('classGroupschedule'));
        } catch (\Throwable $th) {
            Log::error('class group schedules Edit Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('update class group schedules');
        $validator = Validator::make($request->all(), [
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'zoom_link' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'Validation Error!');
        }

        try {
            DB::beginTransaction();
            $classGroupschedule = ClassGroupSchedule::findOrFail($id);
            $classGroupschedule->start_time = $request->start_time;
            $classGroupschedule->end_time = $request->end_time;
            $classGroupschedule->zoom_link = $request->zoom_link;
            $classGroupschedule->save();

            DB::commit();
            return redirect()->route('dashboard.class-group-schedules.index', $classGroupschedule->class_group_id)->with('success', 'Schedule Updated Successfully');
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
            Log::error('Schedule Update Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete class group schedules');
        try {
            $classGroupschedule = ClassGroupSchedule::findOrFail($id);
            $classGroupschedule->delete();
            return redirect()->back()->with('success', 'Schedule Deleted Successfully!');
        } catch (\Throwable $th) {
            Log::error('Schedule Delete Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }
}
