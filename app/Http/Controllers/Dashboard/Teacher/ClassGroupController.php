<?php

namespace App\Http\Controllers\Dashboard\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassGroup;
use App\Models\ClassGroupStudent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

            // Transform data into a clean array
            $students = $classGroupStudents->map(function ($student) {
                return [
                    'child_name'     => $student->parentChild->child->name ?? 'N/A',
                    'child_image'    => asset($student->parentChild->child->profile->profile_image ?? 'assets/img/default/user.png'),
                    'parent_name'    => $student->parentChild->parent->name ?? 'N/A',
                ];
            });
            // dd($teachers);
            return view('dashboard.teachers.class-groups.show', compact('classGroup', 'students'));
        } catch (\Throwable $th) {
            Log::error('Teachers Class Groups Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }
}
