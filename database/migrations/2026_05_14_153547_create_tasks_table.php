<?php

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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            //a_faire, en_cours, en_attente, termine
            $table->enum('status', ['a_faire', 'en_cours', 'en_attente', 'termine'])->default('a_faire');
            //faible, moyen, élevé  
            $table->enum('priority', ['low', 'medium', 'high','critical'])->default('medium');
            //date de début et date de fin de la tâche
            $table->date('due_date')->nullable();
            //la tache appartient à un projet
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            //la tache est assignée à un utilisateur
            $table->foreignId('assigned_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
