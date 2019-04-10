<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTransactionCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_category', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name', 100)->nullable(false);
            $table->integer('type_id')->unsigned()->nullable(false);
            $table->integer('transaction_type_id')->unsigned()->nullable(false);

            $table->foreign('type_id', 'fk_transaction_category_type_id')
                ->references('id')
                ->on('transaction_category_type')
            ;
            $table->foreign('transaction_type_id', 'fk_transaction_category_transaction_type_id')
                ->references('id')
                ->on('transaction_type')
            ;
        });

        // Insert default categories
        DB::table('transaction_category')->insert(
            [
                ['transaction_type_id' => 1, 'type_id' => 5, 'name' => 'salary'],
                ['transaction_type_id' => 1, 'type_id' => 5, 'name' => 'deposits'],
                ['transaction_type_id' => 1, 'type_id' => 5, 'name' => 'gifts'],
                ['transaction_type_id' => 1, 'type_id' => 5, 'name' => 'cashback services'],
                ['transaction_type_id' => 1, 'type_id' => 5, 'name' => 'selling'],
                ['transaction_type_id' => 1, 'type_id' => 5, 'name' => 'other active income'],
                ['transaction_type_id' => 1, 'type_id' => 5, 'name' => 'other passive income'],
                ['transaction_type_id' => 2, 'type_id' => 1, 'name' => 'food and supermarkets'],
                ['transaction_type_id' => 2, 'type_id' => 1, 'name' => 'pets'],
                ['transaction_type_id' => 2, 'type_id' => 1, 'name' => 'mobile top up'],
                ['transaction_type_id' => 2, 'type_id' => 1, 'name' => 'flat rent and utility bills'],
                ['transaction_type_id' => 2, 'type_id' => 1, 'name' => 'beauty and medicine'],
                ['transaction_type_id' => 2, 'type_id' => 1, 'name' => 'other bills'],
                ['transaction_type_id' => 2, 'type_id' => 2, 'name' => 'household appliances'],
                ['transaction_type_id' => 2, 'type_id' => 2, 'name' => 'furniture'],
                ['transaction_type_id' => 2, 'type_id' => 2, 'name' => 'clothes and shoes'],
                ['transaction_type_id' => 2, 'type_id' => 2, 'name' => 'education'],
                ['transaction_type_id' => 2, 'type_id' => 2, 'name' => 'other goods for home'],
                ['transaction_type_id' => 2, 'type_id' => 3, 'name' => 'cafes and restaurants'],
                ['transaction_type_id' => 2, 'type_id' => 3, 'name' => 'paid subscriptions'],
                ['transaction_type_id' => 2, 'type_id' => 3, 'name' => 'travelling'],
                ['transaction_type_id' => 2, 'type_id' => 3, 'name' => 'movies'],
                ['transaction_type_id' => 2, 'type_id' => 3, 'name' => 'sport'],
                ['transaction_type_id' => 2, 'type_id' => 3, 'name' => 'other entertainments'],
                ['transaction_type_id' => 2, 'type_id' => 4, 'name' => 'deposits'],
                ['transaction_type_id' => 2, 'type_id' => 4, 'name' => 'investments'],
                ['transaction_type_id' => 2, 'type_id' => 4, 'name' => 'moneybox'],
                ['transaction_type_id' => 2, 'type_id' => 4, 'name' => 'other money savings'],
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_category');
    }
}
