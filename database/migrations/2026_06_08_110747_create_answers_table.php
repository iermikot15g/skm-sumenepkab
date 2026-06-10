<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke responden
            $table->foreignId('respondent_id')->constrained()->onDelete('cascade');
            
            // Data jawaban
            $table->tinyInteger('question_number'); // 1 s/d 9
            $table->tinyInteger('score');           // 1, 2, 3, atau 4
            
            $table->timestamps();
            
            // Satu responden hanya bisa menjawab 1x per nomor pertanyaan
            $table->unique(['respondent_id', 'question_number'], 'unique_answer_per_respondent');
            
            $table->index('question_number');
            $table->index('score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};