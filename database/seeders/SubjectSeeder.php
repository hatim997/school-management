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
            ['name' => 'English', 'code' => 'EN-0001', 'price' => '100', 'duration' => '10'],
            ['name' => 'Mathematics', 'code' => 'MTH-0002', 'price' => '180', 'duration' => '15'],
            ['name' => 'Physics', 'code' => 'PHY-0003', 'price' => '220', 'duration' => '18'],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
