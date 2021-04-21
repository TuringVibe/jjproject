<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTabelAddAndModifyColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table){
            $table->dropColumn('name');
            $table->dropUnique('users_email_unique');
        });

        Schema::table('users',function(Blueprint $table){
            $table->string('firstname',255)->after('id');
            $table->string('lastname',255)->nullable()->after('firstname');
            $table->string('email',255)->change();
            $table->enum('role',["user","admin"])->after('email_verified_at');
            $table->string('salt',10)->after('role');
            $table->string('password',255)->after('salt')->change();
            $table->string('img_path',255)->nullable()->after('remember_token');
            $table->string('phone',15)->nullable()->after('img_path');
            $table->string('address',255)->nullable()->after('phone');
            $table->string('city',255)->nullable()->after('address');
            $table->string('state',255)->nullable()->after('city');
            $table->string('country',255)->nullable()->after('state');
            $table->integer('zip_code')->nullable()->after('country');
            $table->foreignId('created_by')->default(0)->after('created_at');
            $table->foreignId('updated_by')->default(0)->after('updated_at');
            $table->softDeletes()->after('updated_by');
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
        Schema::table('users',function(Blueprint $table){
            $table->id();
            $table->string('name')->after('id');
            $table->string('email')->unique()->change();
            $table->string('password')->change();
            $table->dropColumn('firstname');
            $table->dropColumn('lastname');
            $table->dropColumn('role');
            $table->dropColumn('salt');
            $table->string('password')->after('email_verified_at')->change();
            $table->dropColumn('img_path');
            $table->dropColumn('phone');
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('country');
            $table->dropColumn('zip_code');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
            $table->dropColumn('deleted_at');
            $table->dropColumn('deleted_by');
        });
    }
}
