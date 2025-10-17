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
use App\Models\Billing;
use App\Models\ChildSubject;
use App\Models\ClassGroup;
use App\Models\ClassGroupStudent;
use App\Models\Payment;
use App\Models\Subject;
use App\Models\TeacherSubject;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
            $subjects = Subject::where('is_active', 'active')->get();
            $billing = Billing::where('user_id', auth()->user()->id)->first();
            $genders = Gender::where('is_active', 'active')->get();
            return view('dashboard.parents.children.create', compact('genders','subjects','billing'));
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
            // Child required fields
            'child_name' => 'required|string|max:255',
            'child_email' => 'required|email|unique:users,email',
            'dob' => 'required|date',
            'gender_id' => 'required|exists:genders,id',

            // Base required fields
            'payment_method' => 'required|in:card,paypal',
            'subject_id' => 'required|exists:subjects,id',
            'amount' => 'required|string|max:255',

            // Billing details
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip' => 'required|string|max:20',
            'address' => 'required|string|max:500',

            // Conditional validation for Card payments only
            'paymentCard' => 'required_if:payment_method,card|nullable|string|min:16|max:25',
            'paymentCardName' => 'required_if:payment_method,card|nullable|string|max:255',
            'paymentCardExpiryDate' => "required_if:payment_method,card|nullable|string",
            'paymentCardCvv' => 'required_if:payment_method,card|nullable|digits_between:3,4',
        ]);

        if ($validator->fails()) {
            Log::error('Children Store Failed', ['error' => $validator->errors()]);
            return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'Validation Error!');
        }
        try {
            DB::beginTransaction();
            $billing = Billing::where('user_id', auth()->user()->id)->first();
            if (!$billing) {
                $billing = new Billing();
                $billing->user_id = auth()->user()->id;
            }
            $billing->name = $request->name;
            $billing->email = $request->email;
            $billing->phone = $request->phone;
            $billing->country = $request->country;
            $billing->state = $request->state;
            $billing->city = $request->city;
            $billing->zip = $request->zip;
            $billing->address = $request->address;
            $billing->card_name = $request->paymentCardName;
            $billing->card_number = $request->paymentCard;
            $billing->card_exp = $request->paymentCardExpiryDate;
            $billing->card_cvv = $request->paymentCardCvv;
            $billing->save();

            // --- 3️⃣ Check or Create Class Group ---
            $subjectId = $request->subject_id;
            $subject = Subject::findOrFail($subjectId);

            // Calculate child's age from DOB
            $childAge = Carbon::parse($request->dob)->age;

            // Check if student's age is within subject range
            if ($childAge < $subject->from_age || $childAge > $subject->to_age) {
                return redirect()->back()
                    ->withInput($request->all())
                    ->with('error', "This course is only for ages {$subject->from_age} to {$subject->to_age}. Your child's age is {$childAge}, which is not eligible.");
            }

            // Find existing class group with available capacity
            $classGroup = ClassGroup::where('subject_id', $subjectId)
                ->where('is_active', 'active')
                ->whereRaw('(SELECT COUNT(*) FROM class_group_students WHERE class_group_students.class_group_id = class_groups.id) < class_groups.capacity')
                ->first();

            if (!$classGroup) {
                // Get all active teachers who teach this subject
                $availableTeachers = TeacherSubject::where('subject_id', $subjectId)
                    ->where('is_active', 'active')
                    ->pluck('teacher_id');

                if ($availableTeachers->isEmpty()) {
                    DB::rollBack();
                    return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'No teacher available for this subject at the moment.');
                }

                // Exclude teachers already assigned to a class group for this subject
                $teachersAlreadyAssigned = ClassGroup::where('subject_id', $subjectId)
                    ->pluck('teacher_id');

                $eligibleTeachers = $availableTeachers->diff($teachersAlreadyAssigned);

                if ($eligibleTeachers->isEmpty()) {
                    DB::rollBack();
                    return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'All teachers for this subject already have assigned groups.');
                }

                // Pick one teacher (random or by your logic)
                $teacherId = $eligibleTeachers->random();

                // --- Create new class group ---
                $existingGroupsCount = ClassGroup::where('subject_id', $subjectId)->count();
                $groupLetter = chr(65 + $existingGroupsCount);

                $classGroup = new ClassGroup();
                $classGroup->name = "Group - " . $groupLetter;
                $classGroup->subject_id = $subjectId;
                $classGroup->teacher_id = $teacherId;
                $classGroup->min_age = 0;
                $classGroup->max_age = 0;
                $classGroup->capacity = 15;
                $classGroup->is_active = 'active';
                $classGroup->save();
            }

            $plainPassword = Str::random(8) . substr(str_shuffle('!@#$%^&*'), 0, 2);
            $plainPassword = substr(str_shuffle($plainPassword), 0, 8);

            $child = new User();
            $child->name = $request->child_name;
            $child->email = $request->child_email;
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

            // --- 4️⃣ Add Student to Class Group ---
            $classGroupStudent = new ClassGroupStudent();
            $classGroupStudent->class_group_id = $classGroup->id;
            $classGroupStudent->parent_child_id = $parentChild->id;
            $classGroupStudent->save();

            $childSubject = new ChildSubject();
            $childSubject->parent_child_id = $parentChild->id;
            $childSubject->subject_id = $request->subject_id;
            $childSubject->save();

            $payment = new Payment();
            $tempTransactionId = 'TRANS-' . date('Y') . '-' . uniqid();
            $payment->transaction_id = $tempTransactionId;
            $payment->parent_child_id = $parentChild->id;
            $payment->subject_id = $request->subject_id;
            $payment->billing_id = $billing->id;
            $payment->payment_method = $request->payment_method;
            $payment->amount = $request->amount;
            $payment->payment_status = 'success';
            $payment->save();
            $payment->transaction_id = 'TRANS-' . date('Y') . '-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT);
            $payment->save();

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

            DB::commit();
            return redirect()->route('dashboard.children.index')->with('success', 'Child created successfully');
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
        $this->authorize('view children');

        try {
            $parentChild = ParentChild::find($id);

            if (!$parentChild) {
                return redirect()->back()->with('error', "Child not found!");
            }

            $child = User::with('profile')->find($parentChild->child_id);

            // Get all subjects and related groups of the child
            $childSubjects = ChildSubject::with(['subject'])
                ->where('parent_child_id', $id)
                ->get();

            $completedSubjects = $childSubjects->where('status', 'complete')->count();

            // Get all groups where the child is enrolled (with subject + teacher)
            $groupDetails = ClassGroupStudent::with(['classGroup.subject', 'classGroup.teacher'])
                ->where('parent_child_id', $id)
                ->get();

            return view('dashboard.parents.children.show', compact('child', 'parentChild', 'childSubjects', 'groupDetails', 'completedSubjects'));
        } catch (\Throwable $th) {
            Log::error('Children Show Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
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

    public function generateUsername($name)
    {
        $name = strtolower(str_replace(' ', '', $name));
        $username = $name . rand(1000, 9999);
        return $username;
    }
}
