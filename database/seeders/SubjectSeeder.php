<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'English',
                'code' => 'EN-0001',
                'price' => '100',
                'duration' => '10',
                'short_description' => 'Completed English Course with USA Accent.',
                'rating' => 4.4,
                'total_enrolled' => 112,
            ],
            [
                'name' => 'Mathematics',
                'code' => 'MTH-0002',
                'price' => '180',
                'duration' => '15',
                'short_description' => 'Completed Mathematics Course with Algebra and other Maths problems.',
                'rating' => 4.2,
                'total_enrolled' => 88
            ],
            [
                'name' =>
                'Physics',
                'code' => 'PHY-0003',
                'price' => '220',
                'duration' => '18',
                'short_description' => 'Completed Physics Course with many practical works.',
                'rating' => 4.6,
                'total_enrolled' => 107
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
