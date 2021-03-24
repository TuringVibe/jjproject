<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceMutationSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_mutation_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('next_mutation_date');
            $table->string('name',100);
            $table->integer('nominal');
            $table->enum('mode',["debit","credit"]);
            $table->foreignId('project_id')->nullable();
            $table->json('attached_label_ids')->nullable();
            $table->enum('repeat',["daily", "weekly", "monthly", "yearly"])->default("daily");
            $table->string('notes',255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('finance_mutation_schedules', function(Blueprint $table) {
            $table->foreignId('created_by')->default(0)->after('created_at');
            $table->foreignId('updated_by')->default(0)->after('updated_at');
            $table->foreignId('deleted_by')->nullable()->after('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finance_mutation_schedules');
    }
}
