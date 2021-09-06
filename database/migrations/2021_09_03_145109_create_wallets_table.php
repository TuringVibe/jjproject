<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string("name",100);
            $table->enum("default_currency",["usd","cny","idr"]);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('wallets', function(Blueprint $table) {
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
        Schema::dropIfExists('wallets');
    }
}
