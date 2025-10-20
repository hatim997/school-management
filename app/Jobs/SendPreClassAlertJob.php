<?php

namespace App\Jobs;

use App\Models\ClassGroupSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPreClassAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $now = now();
        $fifteenMinLater = $now->copy()->addMinutes(15);
        $today = strtolower($now->format('l'));

        $schedules = ClassGroupSchedule::with([
            'classGroup.teacher',
            'classGroup.subject',
            'classGroup.classGroupStudents.parentChild.child',
            ])
            ->where('day', $today)
            ->whereTime('start_time', '>=', $now->format('H:i:s'))
            ->whereTime('start_time', '<=', $fifteenMinLater->format('H:i:s'))
            ->get();

        foreach ($schedules as $schedule) {
            // Send notification to teacher
            $teacher = $schedule->classGroup->teacher;
            $className = $schedule->classGroup->name;
            $subjectName = $schedule->classGroup->subject->name;
            $classId = $schedule->classGroup->id;
            $message = "Reminder: Your class '$className' of '{$subjectName}' starts in 15 minutes!";
            if (app()->bound('notificationService')) {
                app('notificationService')->notifyUsers(
                    [$teacher],
                    $message,
                    'class_groups',
                    $classId
                );
            }
            foreach ($schedule->classGroup->classGroupStudents as $student) {
                $child = $student->parentChild->child;
                $className = $schedule->classGroup->name;
                $subjectName = $schedule->classGroup->subject->name;
                $classId = $schedule->classGroup->id;
                $message = "Reminder: Your class '$className' of '{$subjectName}' starts in 15 minutes!";
                if (app()->bound('notificationService')) {
                    app('notificationService')->notifyUsers(
                        [$child],
                        $message,
                        'class_groups',
                        $classId
                    );
                }
            }
        }
    }
}
