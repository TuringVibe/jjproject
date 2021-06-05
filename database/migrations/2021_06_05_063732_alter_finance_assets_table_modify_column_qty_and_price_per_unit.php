<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFinanceAssetsTableModifyColumnQtyAndPricePerUnit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_assets', function(Blueprint $table){
            $table->decimal('qty',25,15)->change();
            $table->decimal('buy_price_per_unit',25,15)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finance_assets',function(Blueprint $table){
            $table->decimal('qty',20,3)->change();
            $table->decimal('buy_price_per_unit',20,3)->change();
        });
    }
}
