<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableEventsModifyColumnRepeat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events',function(Blueprint $table) {
            $table->dropColumn('repeat');
        });
        Schema::table('events',function(Blueprint $table) {
            $table->enum('repeat',['once','daily','weekly','biweekly','monthly','yearly']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events',function(Blueprint $table) {
            $table->dropColumn('repeat');
        });
        Schema::table('events',function(Blueprint $table) {
            $table->enum('repeat',['once','daily','weekday','weekend','weekly','monthly','yearly'])->change();
        });
    }
}
