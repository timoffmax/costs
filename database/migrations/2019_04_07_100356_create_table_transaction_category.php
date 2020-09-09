<?php

use App\TransactionType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateTableTransactionCategory
 */
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

        $typeLiving = DB::table('transaction_category_type')
            ->select('id')
            ->where(['name' => 'living'])
            ->first()
        ;

        $typeLiveImproving = DB::table('transaction_category_type')
            ->select('id')
            ->where(['name' => 'life improving'])
            ->first()
        ;

        $typeEntertainments = DB::table('transaction_category_type')
            ->select('id')
            ->where(['name' => 'entertainments'])
            ->first()
        ;

        $typeMoneySavings = DB::table('transaction_category_type')
            ->select('id')
            ->where(['name' => 'money savings'])
            ->first()
        ;

        $typeIncomes = DB::table('transaction_category_type')
            ->select('id')
            ->where(['name' => 'incomes'])
            ->first()
        ;

        $typeServiceChanges = DB::table('transaction_category_type')
            ->select('id')
            ->where(['name' => 'service charges'])
            ->first()
        ;

        $typeEducation = DB::table('transaction_category_type')
            ->select('id')
            ->where(['name' => 'education'])
            ->first()
        ;

        $typeGifts = DB::table('transaction_category_type')
            ->select('id')
            ->where(['name' => 'gifts'])
            ->first()
        ;

        // Insert default categories
        DB::table('transaction_category')->insert(
            [
                ['transaction_type_id' => 1, 'type_id' => $typeIncomes->id, 'name' => 'salary'],
                ['transaction_type_id' => 1, 'type_id' => $typeIncomes->id, 'name' => 'deposits'],
                ['transaction_type_id' => 1, 'type_id' => $typeIncomes->id, 'name' => 'gifts'],
                ['transaction_type_id' => 1, 'type_id' => $typeIncomes->id, 'name' => 'cashback services'],
                ['transaction_type_id' => 1, 'type_id' => $typeIncomes->id, 'name' => 'selling'],
                ['transaction_type_id' => 1, 'type_id' => $typeIncomes->id, 'name' => 'other active income'],
                ['transaction_type_id' => 1, 'type_id' => $typeIncomes->id, 'name' => 'other passive income'],
                ['transaction_type_id' => 2, 'type_id' => $typeLiving->id, 'name' => 'food and supermarkets'],
                ['transaction_type_id' => 2, 'type_id' => $typeLiving->id, 'name' => 'pets'],
                ['transaction_type_id' => 2, 'type_id' => $typeLiving->id, 'name' => 'mobile top up'],
                ['transaction_type_id' => 2, 'type_id' => $typeLiving->id, 'name' => 'flat rent and utility bills'],
                ['transaction_type_id' => 2, 'type_id' => $typeLiving->id, 'name' => 'beauty and medicine'],
                ['transaction_type_id' => 2, 'type_id' => $typeLiving->id, 'name' => 'other bills'],
                ['transaction_type_id' => 2, 'type_id' => $typeLiveImproving->id, 'name' => 'household appliances'],
                ['transaction_type_id' => 2, 'type_id' => $typeLiveImproving->id, 'name' => 'tech devices'],
                ['transaction_type_id' => 2, 'type_id' => $typeLiveImproving->id, 'name' => 'furniture'],
                ['transaction_type_id' => 2, 'type_id' => $typeLiveImproving->id, 'name' => 'clothes and shoes'],
                ['transaction_type_id' => 2, 'type_id' => $typeLiveImproving->id, 'name' => 'education'],
                ['transaction_type_id' => 2, 'type_id' => $typeLiveImproving->id, 'name' => 'other goods for home'],
                ['transaction_type_id' => 2, 'type_id' => $typeEntertainments->id, 'name' => 'cafes and restaurants'],
                ['transaction_type_id' => 2, 'type_id' => $typeEntertainments->id, 'name' => 'paid subscriptions'],
                ['transaction_type_id' => 2, 'type_id' => $typeEntertainments->id, 'name' => 'travelling'],
                ['transaction_type_id' => 2, 'type_id' => $typeEntertainments->id, 'name' => 'movies'],
                ['transaction_type_id' => 2, 'type_id' => $typeEntertainments->id, 'name' => 'sport'],
                ['transaction_type_id' => 2, 'type_id' => $typeEntertainments->id, 'name' => 'events'],
                ['transaction_type_id' => 2, 'type_id' => $typeEntertainments->id, 'name' => 'other entertainments'],
                ['transaction_type_id' => 2, 'type_id' => $typeMoneySavings->id, 'name' => 'deposits'],
                ['transaction_type_id' => 2, 'type_id' => $typeMoneySavings->id, 'name' => 'investments'],
                ['transaction_type_id' => 2, 'type_id' => $typeMoneySavings->id, 'name' => 'moneybox'],
                ['transaction_type_id' => 2, 'type_id' => $typeMoneySavings->id, 'name' => 'other money savings'],
                ['transaction_type_id' => 2, 'type_id' => $typeEducation->id, 'name' => 'courses'],
                ['transaction_type_id' => 2, 'type_id' => $typeEducation->id, 'name' => 'books'],
                ['transaction_type_id' => 2, 'type_id' => $typeEducation->id, 'name' => 'events'],
                ['transaction_type_id' => 2, 'type_id' => $typeServiceChanges->id, 'name' => 'service charge'],
                ['transaction_type_id' => 2, 'type_id' => $typeServiceChanges->id, 'name' => 'service fee'],
                ['transaction_type_id' => 1, 'type_id' => $typeGifts->id, 'name' => 'gifts'],
                ['transaction_type_id' => 2, 'type_id' => $typeGifts->id, 'name' => 'gifts'],
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
