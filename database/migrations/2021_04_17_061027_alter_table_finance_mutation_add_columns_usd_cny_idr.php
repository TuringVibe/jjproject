<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableFinanceMutationAddColumnsUsdCnyIdr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_mutations',function(Blueprint $table){
            $table->enum('currency',['usd','cny','idr'])->after('name');
            $table->float('nominal')->change();
            $table->float('usd_cny')->after('nominal');
            $table->float('usd_idr')->after('usd_cny');
            $table->float('cny_usd')->after('usd_idr');
            $table->float('cny_idr')->after('cny_usd');
            $table->float('idr_usd')->after('cny_idr');
            $table->float('idr_cny')->after('idr_usd');
            $table->timestamp('conversion_datetime')->after('idr_cny');
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
            $table->dropColumn('currency');
            $table->integer('nominal')->change();
            $table->dropColumn('usd_cny');
            $table->dropColumn('usd_idr');
            $table->dropColumn('cny_usd');
            $table->dropColumn('cny_idr');
            $table->dropColumn('idr_usd');
            $table->dropColumn('idr_cny');
            $table->dropColumn('conversion_datetime');
        });
    }
}
