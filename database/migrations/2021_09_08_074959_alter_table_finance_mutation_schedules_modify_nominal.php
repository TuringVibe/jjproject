<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableFinanceMutationSchedulesModifyNominal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("finance_mutation_schedules",function(Blueprint $table){
            $table->decimal("nominal",65,30)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("finance_mutation_schedules",function(Blueprint $table){
            $table->decimal("nominal",20,3)->change();
        });
    }
}
