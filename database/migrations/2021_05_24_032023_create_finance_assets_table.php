<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_assets', function (Blueprint $table) {
            $table->id();
            $table->string('name',100);
            $table->decimal('qty',20,3);
            $table->string('unit',10)->nullable();
            $table->timestamp('buy_datetime');
            $table->decimal('buy_price_per_unit',20,3);
            $table->enum('currency',['usd','cny','idr']);
            $table->decimal('usd_cny',25,15);
            $table->decimal('usd_idr',25,15);
            $table->decimal('cny_usd',25,15);
            $table->decimal('cny_idr',25,15);
            $table->decimal('idr_usd',25,15);
            $table->decimal('idr_cny',25,15);
            $table->timestamp('conversion_datetime')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('finance_assets', function(Blueprint $table) {
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
        Schema::dropIfExists('finance_assets');
    }
}
