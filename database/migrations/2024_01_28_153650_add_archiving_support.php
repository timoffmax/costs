<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArchivingSupport extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::table('account', function (Blueprint $table) {
            $table->boolean('is_archived')
                ->nullable(false)
                ->default(0)
                ->after('calculate_costs')
            ;
        });

        Schema::table('place', function (Blueprint $table) {
            $table->boolean('is_archived')
                ->nullable(false)
                ->default(0)
                ->after('name')
            ;
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::table('account', function (Blueprint $table) {
            $table->dropColumn('is_archived');
        });

        Schema::table('place', function (Blueprint $table) {
            $table->dropColumn('is_archived');
        });
    }
}
