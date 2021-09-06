<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterFinanceMutationSchedulesTableAddFromWalletIdAndToWalletIdColumnsAndModifyMode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_mutation_schedules', function (Blueprint $table) {
            $table->foreignId("from_wallet_id")->nullable()->after("mode");
            $table->foreignId("to_wallet_id")->nullable()->after("from_wallet_id");
        });
        DB::statement("ALTER TABLE finance_mutation_schedules MODIFY COLUMN mode ENUM('debit', 'credit', 'transfer')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finance_mutation_schedules', function (Blueprint $table) {
            $table->dropColumn(["from_wallet_id","to_wallet_id"]);
        });
        DB::statement("ALTER TABLE finance_mutation_schedules MODIFY COLUMN mode ENUM('debit', 'credit')");
    }
}
