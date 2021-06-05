<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAssetPriceChangesTableModifyColumnPricePerUnit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asset_price_changes', function(Blueprint $table){
            $table->decimal('price_per_unit',25,15)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asset_price_changes', function(Blueprint $table){
            $table->decimal('price_per_unit',20,3)->change();
        });
    }
}
