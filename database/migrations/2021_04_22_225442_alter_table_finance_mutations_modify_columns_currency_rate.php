<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableFinanceMutationsModifyColumnsCurrencyRate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_mutations',function(Blueprint $table){
            $table->decimal('nominal',20,3)->change();
            $table->decimal('usd_cny',25,15)->change();
            $table->decimal('usd_idr',25,15)->change();
            $table->decimal('cny_usd',25,15)->change();
            $table->decimal('cny_idr',25,15)->change();
            $table->decimal('idr_usd',25,15)->change();
            $table->decimal('idr_cny',25,15)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finance_mutations',function(Blueprint $table){
            $table->float('nominal')->change();
            $table->float('usd_cny')->change();
            $table->float('usd_idr')->change();
            $table->float('cny_usd')->change();
            $table->float('cny_idr')->change();
            $table->float('idr_usd')->change();
            $table->float('idr_cny')->change();
        });
    }
}
