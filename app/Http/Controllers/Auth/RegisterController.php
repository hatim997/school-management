<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function register()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        } else {
            $subjects = Subject::where('is_active', 'active')->get();
            return view('auth.register', compact('subjects'));
        }
    }

    public function register_attempt(Request $request){

        $rules = [
            'role' => 'required|in:parent,teacher',
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'min:6'
            ],
            // 'password' => [
            //     'required',
            //     Password::min(8)
            //     ->letters()
            //     ->mixedCase()
            //     ->numbers()
            //     ->symbols()
            // ],
            'confirm-password' => 'required|same:password',
            'subject_id' => 'required_if:role,teacher|array|min:1',
            'subject_id.*' => 'required_if:role,teacher|exists:subjects,id',
            'qualifications' => 'nullable|required_if:role,teacher|string|max:255',
            'terms' => 'required|string|max:255',
        ];

        // Make 'g-recaptcha-response' nullable if CAPTCHA is not enabled
        if (config('captcha.version') !== 'no_captcha') {
            $rules['g-recaptcha-response'] = 'required|captcha';
        } else {
            $rules['g-recaptcha-response'] = 'nullable';
        }

        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()){
            // Log the validation errors for debugging
            Log::error('Validation failed', $validate->errors()->all());
            return Redirect::back()->withErrors($validate)->withInput($request->all())->with('error', 'Validation Error!');
        }
        try{
            // Begin a transaction
            DB::beginTransaction();
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->role == 'teacher') {
                $user->is_active = 'pending';
            }
            $user->email_verified_at = now();
            $user->password = Hash::make($request->password);


            $username = $this->generateUsername($request->name);

            while (User::where('username', $username)->exists()) {
                $username = $this->generateUsername($request->name);
            }
            $user->username = $username;
            $user->save();

            $user->syncRoles($request->role);

            if ($request->role == 'teacher') {
                foreach ($request->subject_id as $subjectId) {
                    $teacherSubject = new TeacherSubject();
                    $teacherSubject->teacher_id = $user->id;
                    $teacherSubject->subject_id = $subjectId;
                    $teacherSubject->save();
                }
            }

            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->first_name = $request->name;
            if ($request->role == 'teacher') {
                $profile->qualifications = $request->qualifications;
            }
            $profile->save();

            // Attempt to authenticate
            Auth::attempt(['email' => $request->email, 'password' => $request->password]);

            if (Auth::check()) {

                // VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
                //     return (new MailMessage)
                //         ->subject('Verify Email Address')
                //         ->line('Click the button below to verify your email address.')
                //         ->action('Verify Email Address', $url);
                // });
            }
            app('notificationService')->notifyUsers([$user], 'Welcome to ' . Helper::getCompanyName(), null, null);
            // $user->sendEmailVerificationNotification();

            // Commit the transaction
            DB::commit();

            return redirect()->route('dashboard')->with('success','Your account has been created successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            // Log the error for debugging
            Log::error('User registration failed', ['error' => $th->getMessage()]);
            return redirect()->back()->withInput($request->all())->with('error', "Something went wrong! Please try again later");
            // throw $th;
        }
    }

    public function generateUsername($name)
    {
        $name = strtolower(str_replace(' ', '', $name));
        $username = $name . rand(1000, 9999);
        return $username;
    }

    public function checkUsername(Request $request)
    {
        $username = $request->username;
        $user = User::where('username', $username)->first();
        if ($user) {
            return response()->json([
                'status' => 'matched',
                'user' => $user,
            ]);
        } else {
            return response()->json(['status' => 'not_matched']);
        }
    }
}
