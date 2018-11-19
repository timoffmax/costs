<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable(false);
            $table->integer('account_id')->unsigned()->nullable(false);
            $table->integer('type_id')->unsigned()->nullable(false);
            $table->decimal('sum', 10, 2)->default('0.00');
            $table->decimal('balance_before', 10, 2)->default('0.00');
            $table->decimal('balance_after', 10, 2)->default('0.00');
            $table->dateTime('date')->nullable(false);
            $table->string('comment', 300)->nullable(true);
            $table->timestamps();

            $table->foreign('type_id', 'fk_transaction_type_id')
                ->references('id')
                ->on('transaction_type');
            $table->foreign('account_id', 'fk_transaction_account_id')
                ->references('id')
                ->on('account');
            $table->foreign('user_id', 'fk_transaction_user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction');
    }
}
