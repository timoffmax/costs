<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->integer('category_id')->unsigned()->default(null)->after('type_id');

            $table->foreign('category_id', 'fk_transaction_transaction_category_id')
                ->references('id')
                ->on('transaction_category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->dropForeign('fk_transaction_transaction_category_id');

            $table->dropColumn('category_id');
        });
    }
}
