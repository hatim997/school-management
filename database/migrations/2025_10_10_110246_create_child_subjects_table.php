<?php

use App\Models\ParentChild;
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
        Schema::create('child_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ParentChild::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Subject::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->enum('status', ['pending', 'inprogress', 'complete'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_subjects');
    }
};
