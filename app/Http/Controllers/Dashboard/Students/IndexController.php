<?php

namespace App\Http\Controllers\Dashboard\Students;

use App\Http\Controllers\Controller;
use App\Models\ChildSubject;
use App\Models\ParentChild;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
    public function enrolledSubjects()
    {
        try {
            $parentChild = ParentChild::where('child_id', auth()->user()->id)->first();
            $studentSubjects = ChildSubject::with('subject')->where('parent_child_id', $parentChild->id)->get();
            // dd($teachers);
            return view('dashboard.students.enrolled_subjects', compact('studentSubjects'));
        } catch (\Throwable $th) {
            Log::error('Students Enrolled Subjects Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }
    public function enrolledSubjectShow($id)
    {
        try {
            $subject = Subject::with('subject')->findOrFail($id);
            // dd($teachers);
            return view('dashboard.students.enrolled_subjects', compact('studentSubjects'));
        } catch (\Throwable $th) {
            Log::error('Students Enrolled Subjects Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }
}
