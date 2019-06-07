<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateTableTransactionCategoryType
 */
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
                ['name' => 'service charges'],
                ['name' => 'education'],
                ['name' => 'gifts'],
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
