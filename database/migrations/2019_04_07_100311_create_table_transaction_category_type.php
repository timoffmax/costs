<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTransactionCategoryType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_category_type', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name', 50)->nullable(false);
        });

        // Insert default types
        DB::table('transaction_category_type')->insert(
            [
                ['name' => 'living'],
                ['name' => 'life improving'],
                ['name' => 'entertainments'],
                ['name' => 'money savings'],
                ['name' => 'incomes'],
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
        Schema::dropIfExists('transaction_category_type');
    }
}
