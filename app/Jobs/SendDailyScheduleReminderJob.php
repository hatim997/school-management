<?php

namespace App\Jobs;

use App\Models\ClassGroupSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDailyScheduleReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $today = strtolower(now()->format('l')); // e.g., monday

        // Load all class schedules for today + relationships
        $schedules = ClassGroupSchedule::with([
            'classGroup.teacher',
            'classGroup.subject',
            'classGroup.classGroupStudents.parentChild.child',
        ])
        ->where('day', $today)
        ->get();

        foreach ($schedules as $schedule) {
            $classGroup = $schedule->classGroup;
            if (!$classGroup) continue;

            $className = $classGroup->name;
            $subjectName = $classGroup->subject->name;
            $classId   = $classGroup->id;

            // ✅ Convert time to AM/PM format
            $startTime = date('h:i A', strtotime($schedule->start_time));

            // --- 🎓 1. Notify the teacher ---
            $teacher = $classGroup->teacher;
            if ($teacher && !empty($teacher->email)) {
                $message = "You are scheduled to teach '{$className}' of '{$subjectName}' today at {$startTime}.";

                // Optional: App notification
                if (app()->bound('notificationService')) {
                    app('notificationService')->notifyUsers(
                        [$teacher],
                        $message,
                        'class_groups',
                        $classId
                    );
                }

                // ✅ Send Email (now safely checks for valid email)
                try {
                    Mail::raw($message, function ($m) use ($teacher) {
                        $m->to($teacher->email)
                          ->subject("Today's Teaching Reminder");
                    });
                } catch (\Exception $e) {
                    Log::error("Failed to send teacher email: " . $e->getMessage());
                }

                sleep(1); // throttle
            }

            // --- 👩‍🎓 2. Notify all students ---
            foreach ($classGroup->classGroupStudents as $groupStudent) {
                $child = $groupStudent->parentChild->child ?? null;
                if (!$child || empty($child->email)) continue;

                $message = "You have class '{$className}' of '{$subjectName}' today at {$startTime}.";

                if (app()->bound('notificationService')) {
                    app('notificationService')->notifyUsers(
                        [$child],
                        $message,
                        'class_groups',
                        $classId
                    );
                }

                try {
                    Mail::raw($message, function ($m) use ($child) {
                        $m->to($child->email)
                          ->subject("Today's Class Reminder");
                    });
                } catch (\Exception $e) {
                    Log::error("Failed to send student email: " . $e->getMessage());
                }

                sleep(1);
            }
        }
    }
}
