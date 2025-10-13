<?php

namespace App\Http\Controllers\Dashboard\Parents;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\ChildSubject;
use App\Models\ClassGroup;
use App\Models\ClassGroupStudent;
use App\Models\ParentChild;
use App\Models\Payment;
use App\Models\Subject;
use App\Models\TeacherSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index($id)
    {
        try {
            $subject = Subject::findOrFail($id);
            $parentChildrens = ParentChild::with('child')->where('parent_id', auth()->user()->id)->get();
            return view('dashboard.parents.subjects.checkout', compact('subject', 'parentChildrens'));
        } catch (\Throwable $th) {
            Log::error('Checkout Index Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    public function submitCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Base required fields
            'payment_method' => 'required|in:card,paypal',
            'child_id' => 'required|exists:parent_children,id',
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
            $child = ParentChild::findOrFail($request->child_id);

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
                    return redirect()->back()->with('error', 'No teacher available for this subject at the moment.');
                }

                // Exclude teachers already assigned to a class group for this subject
                $teachersAlreadyAssigned = ClassGroup::where('subject_id', $subjectId)
                    ->pluck('teacher_id');

                $eligibleTeachers = $availableTeachers->diff($teachersAlreadyAssigned);

                if ($eligibleTeachers->isEmpty()) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'All teachers for this subject already have assigned groups.');
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

            // --- 4️⃣ Add Student to Class Group ---
            $classGroupStudent = new ClassGroupStudent();
            $classGroupStudent->class_group_id = $classGroup->id;
            $classGroupStudent->parent_child_id = $child->id;
            $classGroupStudent->save();

            $childSubject = new ChildSubject();
            $childSubject->parent_child_id = $request->child_id;
            $childSubject->subject_id = $request->subject_id;
            $childSubject->save();

            $payment = new Payment();
            $tempTransactionId = 'TRANS-' . date('Y') . '-' . uniqid();
            $payment->transaction_id = $tempTransactionId;
            $payment->parent_child_id = $request->child_id;
            $payment->subject_id = $request->subject_id;
            $payment->billing_id = $billing->id;
            $payment->payment_method = $request->payment_method;
            $payment->amount = $request->amount;
            $payment->payment_status = 'success';
            $payment->save();
            $payment->transaction_id = 'TRANS-' . date('Y') . '-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT);
            $payment->save();
            DB::commit();
            return redirect()->route('dashboard.subjects.index')->with('success', "Checkout submitted successfully");
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Checkout Submit Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }
}
