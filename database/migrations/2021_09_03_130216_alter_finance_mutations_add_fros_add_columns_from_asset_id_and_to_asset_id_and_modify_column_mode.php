<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFinanceMutationsAddFrosAddColumnsFromAssetIdAndToAssetIdAndModifyColumnMode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_mutations', function (Blueprint $table) {
            $table->foreignId("wallet_id")->nullable()->after("mode");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finance_mutations', function (Blueprint $table) {
            $table->dropColumn("wallet_id");
        });
    }
}
