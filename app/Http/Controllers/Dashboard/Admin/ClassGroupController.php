<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassGroup;
use App\Models\ClassGroupMaterial;
use App\Models\ClassGroupSchedule;
use App\Models\ClassGroupStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClassGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view class groups');
        try {
            $classGroups = ClassGroup::with('teacher', 'subject', 'classGroupStudents')->get();
            // dd($teachers);
            return view('dashboard.admin.class-groups.index', compact('classGroups'));
        } catch (\Throwable $th) {
            Log::error('Admin Class Groups Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
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

            $classGroupMaterials = ClassGroupMaterial::with('user')->where('class_group_id', $classGroup->id)->get();
            // dd($teachers);
            return view('dashboard.admin.class-groups.show', compact('classGroup', 'students','classGroupSchedules','classGroupMaterials'));
        } catch (\Throwable $th) {
            Log::error('Teachers Class Groups Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
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
