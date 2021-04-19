<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableFinanceMutationSchedulesAddColumnCurrency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_mutation_schedules',function(Blueprint $table){
            $table->enum('currency',['usd','cny','idr'])->after('name');
            $table->float('nominal')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finance_mutation_schedules',function(Blueprint $table){
            $table->dropColumn('currency');
            $table->integer('nominal')->change();
        });
    }
}
