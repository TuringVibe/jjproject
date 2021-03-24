<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateFinanceMutationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_mutations', function (Blueprint $table) {
            $table->id();
            $table->date('mutation_date');
            $table->string('name',100);
            $table->integer('nominal');
            $table->enum('mode',["debit","credit"]);
            $table->foreignId('project_id')->nullable();
            $table->string('notes',255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('finance_mutations', function(Blueprint $table) {
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
        Schema::dropIfExists('finance_mutations');
    }
}
