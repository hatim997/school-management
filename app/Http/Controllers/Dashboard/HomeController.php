<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ClassGroup;
use App\Models\ClassGroupSchedule;
use App\Models\ClassGroupStudent;
use App\Models\Notification;
use App\Models\ParentChild;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = User::findOrFail(Auth::user()->id);
            $unreadNotificationsCount = Notification::where('user_id', $user->id)
                ->whereNull('read_at')
                ->count();

            $todayClass = null;
            $now = Carbon::now();
            $today = strtolower($now->format('l')); // e.g. monday, tuesday

            if ($user->hasRole('student') || $user->hasRole('teacher')) {
                if ($user->hasRole('student')) {
                    $parentChild = ParentChild::where('child_id', $user->id)->first();

                    if ($parentChild) {
                        $classGroupIds = ClassGroupStudent::where('parent_child_id', $parentChild->id)
                            ->pluck('class_group_id');

                        $todayClass = ClassGroup::select('id', 'name', 'teacher_id', 'subject_id')
                            ->with([
                                'subject:id,name',
                                'teacher:id,name',
                                'schedules' => function ($query) use ($today, $now) {
                                    $query->whereRaw('LOWER(day) = ?', [$today])
                                        ->where(function ($q) use ($now) {
                                            $q->where('start_time', '>', $now->format('H:i:s')) // upcoming
                                                ->orWhere(function ($inner) use ($now) {
                                                    $inner->where('start_time', '<=', $now->format('H:i:s'))
                                                        ->where('end_time', '>=', $now->format('H:i:s')); // current
                                                });
                                        })
                                        ->orderBy('start_time', 'asc');
                                }
                            ])
                            ->whereIn('id', $classGroupIds)
                            ->whereHas('schedules', function ($query) use ($today, $now) {
                                $query->whereRaw('LOWER(day) = ?', [$today])
                                    ->where(function ($q) use ($now) {
                                        $q->where('start_time', '>', $now->format('H:i:s'))
                                            ->orWhere(function ($inner) use ($now) {
                                                $inner->where('start_time', '<=', $now->format('H:i:s'))
                                                    ->where('end_time', '>=', $now->format('H:i:s'));
                                            });
                                    });
                            })
                            ->orderBy(
                                ClassGroupSchedule::select('start_time')
                                    ->whereColumn('class_group_id', 'class_groups.id')
                                    ->whereRaw('LOWER(day) = ?', [$today])
                                    ->orderBy('start_time', 'asc')
                                    ->limit(1)
                            )
                            ->first();
                    }
                } else {
                    // Teacher
                    $todayClass = ClassGroup::select('id', 'name', 'teacher_id', 'subject_id')
                        ->with([
                            'subject:id,name',
                            'teacher:id,name',
                            'schedules' => function ($query) use ($today, $now) {
                                $query->whereRaw('LOWER(day) = ?', [$today])
                                    ->where(function ($q) use ($now) {
                                        $q->where('start_time', '>', $now->format('H:i:s'))
                                            ->orWhere(function ($inner) use ($now) {
                                                $inner->where('start_time', '<=', $now->format('H:i:s'))
                                                    ->where('end_time', '>=', $now->format('H:i:s'));
                                            });
                                    })
                                    ->orderBy('start_time', 'asc');
                            }
                        ])
                        ->where('teacher_id', $user->id)
                        ->whereHas('schedules', function ($query) use ($today, $now) {
                            $query->whereRaw('LOWER(day) = ?', [$today])
                                ->where(function ($q) use ($now) {
                                    $q->where('start_time', '>', $now->format('H:i:s'))
                                        ->orWhere(function ($inner) use ($now) {
                                            $inner->where('start_time', '<=', $now->format('H:i:s'))
                                                ->where('end_time', '>=', $now->format('H:i:s'));
                                        });
                                });
                        })
                        ->orderBy(
                            ClassGroupSchedule::select('start_time')
                                ->whereColumn('class_group_id', 'class_groups.id')
                                ->whereRaw('LOWER(day) = ?', [$today])
                                ->orderBy('start_time', 'asc')
                                ->limit(1)
                        )
                        ->first();
                }
            }


            return view('dashboard.index', compact('todayClass', 'unreadNotificationsCount'));
        } catch (\Throwable $th) {
            Log::error("Dashboard Index Failed:" . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again later');
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
}
