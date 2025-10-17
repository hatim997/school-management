<?php

namespace App\Jobs;

use App\Models\ClassGroupSchedule;
use App\Models\StudentAttendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPostClassNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $now = now();
        $today = strtolower($now->format('l'));

        $schedules = ClassGroupSchedule::with([
            'classGroup.subject',
            'classGroup.teacher',
            'classGroup.classGroupStudents.parentChild.child',
        ])
        ->where('day', $today)
        ->whereTime('end_time', '<=', $now->format('H:i:s'))
        ->get();

        foreach ($schedules as $schedule) {
            $classGroup = $schedule->classGroup;
            if (!$classGroup) continue;

            $classId      = $classGroup->id;
            $className    = $classGroup->name;
            $subjectName  = $classGroup->subject->name ?? 'Unnamed Subject';
            $teacher      = $classGroup->teacher;

            // --- 👩‍🏫 1. Notify the teacher ---
            if ($teacher) {
                $teacherMessage = "Today's session for '{$className}' ({$subjectName}) has been completed successfully. Please check the attendance or mark it manually if needed.";

                try {
                    if (app()->bound('notificationService')) {
                        app('notificationService')->notifyUsers(
                            [$teacher],
                            $teacherMessage,
                            'class_groups',
                            $classId
                        );
                    }

                    // Optionally log for debugging
                    Log::info("Post-class teacher notification sent to {$teacher->name} for {$className}.");
                } catch (\Exception $e) {
                    Log::error("Failed to notify teacher {$teacher->name}: " . $e->getMessage());
                }
            }

            // --- 👩‍🎓 2. Notify students who missed attendance ---
            foreach ($classGroup->classGroupStudents as $student) {
                $child = $student->parentChild->child ?? null;
                if (!$child) continue;

                $attendanceExists = StudentAttendance::where('class_group_id', $classId)
                    ->where('student_id', $child->id)
                    ->whereDate('check_in', now()->toDateString())
                    ->exists();

                if (!$attendanceExists) {
                    $message = "You missed class '{$className}' of '{$subjectName}' today.";

                    try {
                        if (app()->bound('notificationService')) {
                            app('notificationService')->notifyUsers(
                                [$child],
                                $message,
                                'class_groups',
                                $classId
                            );
                        }
                    } catch (\Exception $e) {
                        Log::error("Failed to notify student {$child->name}: " . $e->getMessage());
                    }
                }
            }
        }
    }
}
