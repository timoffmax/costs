<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateTableCurrency
 */
class CreateTableCurrency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30)->nullable(false);
            $table->string('sign', 3)->nullable(false);
        });

        // Insert default currencies
        DB::table('currency')->insert(
            [
                ['name' => 'Ukraine Hryvnia', 'sign' => '₴'],
                ['name' => 'United States Dollar', 'sign' => '$'],
                ['name' => 'Euro', 'sign' => '€'],
                ['name' => 'Russia Ruble', 'sign' => '₽'],
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
        Schema::dropIfExists('currency');
    }
}
