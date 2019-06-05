<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ModifyTableAccount
 */
class ModifyTableAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account', function (Blueprint $table) {
            $table->integer('currency_id')
                ->unsigned()
                ->nullable(true)
                ->default(null)
                ->after('user_id')
            ;

            $table->foreign('currency_id', 'fk_account_currency_id')
                ->references('id')
                ->on('currency');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account', function (Blueprint $table) {
            $table->dropForeign('fk_account_currency_id');
            $table->dropColumn('currency_id');
        });
    }
}
