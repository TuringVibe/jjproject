<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceAttachedLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_attached_labels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finance_mutation_id');
            $table->foreignId('finance_label_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('finance_attached_labels', function(Blueprint $table) {
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
        Schema::dropIfExists('finance_attached_labels');
    }
}
