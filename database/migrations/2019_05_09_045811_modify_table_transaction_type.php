<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ModifyTableTransactionType
 */
class ModifyTableTransactionType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add type
        DB::table('transaction_type')->insert(
            [
                ['name' => 'transfer'],
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
        $transferType = DB::table('transaction_type')
            ->select('id')
            ->where(['name' => 'transfer'])
            ->first()
        ;

        if (!empty($transferType)) {
            DB::table('transaction_type')->delete($transferType->id);
        }
    }
}
