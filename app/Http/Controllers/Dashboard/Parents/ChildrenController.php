<?php

namespace App\Http\Controllers\Dashboard\Parents;

use App\Http\Controllers\Controller;
use App\Models\Gender;
use App\Models\ParentChild;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Mail\ChildCreationMail;
use Illuminate\Support\Facades\Mail;

class ChildrenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view children');
        try {
            $childrens = ParentChild::where('parent_id', Auth::user()->id)->with('child.profile:id,user_id,age')->get();
            return view('dashboard.parents.children.index', compact('childrens'));
        } catch (\Throwable $th) {
            Log::error('Children Index Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create children');
        try {
            $genders = Gender::where('is_active', 'active')->get();
            return view('dashboard.parents.children.create', compact('genders'));
        } catch (\Throwable $th) {
            Log::error('Children Create Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create children');
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'dob' => 'required|date',
            'gender_id' => 'required|exists:genders,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'Validation Error!');
        }
        try {
            $plainPassword = Str::random(8) . substr(str_shuffle('!@#$%^&*'), 0, 2);
            $plainPassword = substr(str_shuffle($plainPassword), 0, 8);

            $child = new User();
            $child->name = $request->name;
            $child->email = $request->email;
            $child->password = Hash::make($plainPassword);
            $child->email_verified_at = now();
            $username = $this->generateUsername($request->name);

            while (User::where('username', $username)->exists()) {
                $username = $this->generateUsername($request->name);
            }
            $child->username = $username;
            $child->save();

            $child->assignRole('student');

            $profile = new Profile();
            $profile->user_id = $child->id;
            $profile->first_name = $request->name;
            $profile->dob = $request->dob;
            $profile->gender_id = $request->gender_id;
            $profile->age = now()->diffInYears($request->dob);
            $profile->save();

            $parentChild = new ParentChild();
            $parentChild->parent_id = Auth::user()->id;
            $parentChild->child_id = $child->id;
            $parentChild->temp_pass = $plainPassword;
            $parentChild->save();

            $childData = (object) [
                'parent_name' => Auth::user()->name,
                'child_name' => $child->name,
                'child_email' => $child->email,
                'temp_pass' => $plainPassword,
            ];

            try {
                Mail::to(Auth::user()->email)->send(new ChildCreationMail($childData));
            } catch (\Throwable $th) {
                //throw $th;
                Log::error('Child Creation Mail Failed', ['error' => $th->getMessage()]);
            }

            return redirect()->route('dashboard.children.index')->with('success', 'Child added successfully');
        } catch (\Throwable $th) {
            Log::error('Children Store Failed', ['error' => $th->getMessage()]);
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

    public function generateUsername($name)
    {
        $name = strtolower(str_replace(' ', '', $name));
        $username = $name . rand(1000, 9999);
        return $username;
    }
}
