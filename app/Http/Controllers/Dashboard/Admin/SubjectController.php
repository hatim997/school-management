<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view subject');
        try {
            $subjects = Subject::where('is_active','active')->get();
            return view('dashboard.admin.subjects.index',compact('subjects'));
        } catch (\Throwable $th) {
            Log::error('Subject Index Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create subject');
        try {
            return view('dashboard.admin.subjects.create');
        } catch (\Throwable $th) {
            Log::error('Subject Create Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create subject');
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|unique:subjects,code',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:0',
            'short_description' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rating' => 'required|integer|min:0|max:5',
            'total_enrolled' => 'required|integer|min:0',
            'is_coming' => 'nullable|in:on',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max_size',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'Validation Error!');
        }

        try {
            $isComing = $request->is_coming && $request->is_coming == 'on' ? '1' : '0';
            DB::beginTransaction();
            $subject = new Subject();
            $subject->name = $request->name;
            $subject->code = $request->code;
            $subject->price = $request->price;
            $subject->duration = $request->duration;
            $subject->short_description = $request->short_description;
            $subject->description = $request->description;
            $subject->rating = $request->rating;
            $subject->total_enrolled = $request->total_enrolled;
            $subject->is_coming = $isComing;

            if ($request->hasFile('image')) {
                $bookImage = $request->file('image');
                $bookImage_ext = $bookImage->getClientOriginalExtension();
                $bookImage_name = time() . '_bookImage.' . $bookImage_ext;
                $bookImage_path = 'uploads/subjects';
                $bookImage->move(public_path($bookImage_path), $bookImage_name);
                $subject->image = $bookImage_path . "/" . $bookImage_name;
            }

            $subject->save();

            DB::commit();
            return redirect()->route('dashboard.admin.subjects.index')->with('success', 'Subject Created Successfully');
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
            Log::error('Subject Created Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view subject');
        try {
            $subject = Subject::findOrFail($id);
            return view('dashboard.admin.subjects.index', compact('subject'));
        } catch (\Throwable $th) {
            Log::error('Subject Edit Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorize('update subject');
        try {
            $subject = Subject::findOrFail($id);
            return view('dashboard.admin.subjects.edit', compact('subject'));
        } catch (\Throwable $th) {
            Log::error('Subject Edit Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('update subject');
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|unique:subjects,code,'.$id,
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:0',
            'short_description' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rating' => 'required|integer|min:0|max:5',
            'total_enrolled' => 'required|integer|min:0',
            'is_coming' => 'nullable|in:on',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max_size',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'Validation Error!');
        }

        try {
            $isComing = $request->is_coming && $request->is_coming == 'on' ? '1' : '0';
            DB::beginTransaction();
            $subject = Subject::findOrFail($id);
            $subject->name = $request->name;
            $subject->code = $request->code;
            $subject->price = $request->price;
            $subject->duration = $request->duration;
            $subject->short_description = $request->short_description;
            $subject->description = $request->description;
            $subject->rating = $request->rating;
            $subject->total_enrolled = $request->total_enrolled;
            $subject->is_coming = $isComing;

            if ($request->hasFile('image')) {
                if (isset($subject->image) && File::exists(public_path($subject->image))) {
                    File::delete(public_path($subject->image));
                }

                $bookImage = $request->file('image');
                $bookImage_ext = $bookImage->getClientOriginalExtension();
                $bookImage_name = time() . '_bookImage.' . $bookImage_ext;
                $bookImage_path = 'uploads/subjects';
                $bookImage->move(public_path($bookImage_path), $bookImage_name);
                $subject->image = $bookImage_path . "/" . $bookImage_name;
            }

            $subject->save();

            DB::commit();
            return redirect()->route('dashboard.admin.subjects.index')->with('success', 'Subject Updated Successfully');
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
            Log::error('Subject Created Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete subject');
        try {
            $subject = Subject::findOrFail($id);
            if (isset($subject->image) && File::exists(public_path($subject->image))) {
                File::delete(public_path($subject->image));
            }
            $subject->delete();
            return redirect()->back()->with('success', 'Subject Deleted Successfully!');
        } catch (\Throwable $th) {
            Log::error('Subjects Delete Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }
}
