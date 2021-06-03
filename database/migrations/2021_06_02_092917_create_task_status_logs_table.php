<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskStatusLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id');
            $table->foreignId('task_id');
            $table->enum('last_status',['todo','inprogress','done']);
            $table->string('notes',255);
            $table->timestamp('created_at');
            $table->foreignId('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_status_logs');
    }
}
