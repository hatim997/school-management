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
                'name' => 'Coding',
                'code' => 'CODE-0001',
                'price' => '100',
                'duration' => '10',
                'short_description' => 'Complete Coding Course.',
                'rating' => 4.4,
                'total_enrolled' => 112,
                'image' => 'uploads/subjects/coding.webp',
            ],
            [
                'name' => 'Artifitial Intelligence (AI)',
                'code' => 'AI-0002',
                'price' => '180',
                'duration' => '15',
                'short_description' => 'Complete Artifitial Intelligence (AI) Course.',
                'rating' => 4.2,
                'total_enrolled' => 88,
                'image' => 'uploads/subjects/ai.jpg',
            ],
            [
                'name' => 'Mathematics',
                'code' => 'MATH-0003',
                'price' => '220',
                'duration' => '18',
                'short_description' => 'Complete Mathematics Course.',
                'rating' => 4.6,
                'total_enrolled' => 107,
                'image' => 'uploads/subjects/math.jpg',
            ],
            [
                'name' => 'Reading',
                'code' => 'READ-0004',
                'price' => '220',
                'duration' => '18',
                'short_description' => 'Complete Reading Course.',
                'rating' => 4.6,
                'total_enrolled' => 107,
                'image' => 'uploads/subjects/reading.jpg',
            ],
            [
                'name' => 'Robotics',
                'code' => 'ROBOT-0005',
                'price' => '220',
                'duration' => '18',
                'short_description' => 'Complete Robotics Course.',
                'rating' => 4.6,
                'total_enrolled' => 107,
                'is_coming' => '1',
                'image' => 'uploads/subjects/robotics.jpg',
            ],
            [
                'name' => 'Machine Learning',
                'code' => 'ML-0006',
                'price' => '220',
                'duration' => '18',
                'short_description' => 'Complete Machine Learning Course.',
                'rating' => 4.6,
                'total_enrolled' => 107,
                'is_coming' => '1',
                'image' => 'uploads/subjects/machine-learning.jpg',
            ],
            [
                'name' => 'Abacus',
                'code' => 'AB-0006',
                'price' => '220',
                'duration' => '18',
                'short_description' => 'Complete Abacus Course.',
                'rating' => 4.6,
                'total_enrolled' => 107,
                'is_coming' => '1',
                'image' => 'uploads/subjects/abacus.jpg',
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
