<?php

use App\Models\Subject;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('class_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(Subject::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('teacher_id')
              ->constrained('users')
              ->cascadeOnDelete();
            $table->string('min_age');
            $table->string('max_age');
            $table->integer('capacity');
            $table->enum('is_active', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_groups');
    }
};
