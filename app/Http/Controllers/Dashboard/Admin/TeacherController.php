<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TeacherActivationMail;
use App\Models\ClassGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TeacherController extends Controller
{
    public function index()
    {
        $this->authorize('view teacher');
        try {
            $teachers = User::with('profile', 'teacherClassGroups')->role('teacher')->get();
            // dd($teachers);
            return view('dashboard.admin.teachers.index', compact('teachers'));
        } catch (\Throwable $th) {
            Log::error('Teachers Index Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    public function approveTeacher($id)
    {
        $this->authorize('update teacher');
        try {
            $teacher = User::findOrFail($id);
            $teacher->is_active = 'active';
            $teacher->save();

            // Send activation email
            try {
                Mail::to($teacher->email)->send(new TeacherActivationMail($teacher));
            } catch (\Throwable $th) {
                //throw $th;
            }
            return redirect()->route('dashboard.teachers.index')->with('success', 'Teacher is approved successfully!');
        } catch (\Throwable $th) {
            Log::error('Teachers Approve Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    public function show($id)
    {
        $this->authorize('view teacher');
        try {
            $teacher = User::with('profile')->findOrFail($id);
            $classGroups = ClassGroup::with('subject')->where('teacher_id', $id)->get();
            // dd($teachers);
            return view('dashboard.admin.teachers.show', compact('teacher', 'classGroups'));
        } catch (\Throwable $th) {
            Log::error('Teachers Index Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }
}
