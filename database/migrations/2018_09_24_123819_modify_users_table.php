<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('password');
            $table->integer('role_id')->unsigned()->default(1)->after('name');

            $table->foreign('role_id', 'fk_users_role_id')
                ->references('id')
                ->on('user_role');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('fk_users_role_id');

            $table->dropColumn('role_id');
            $table->dropColumn('photo');
        });
    }
}
