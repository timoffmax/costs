<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ModifyTableTransactionCategoryType
 */
class ModifyTableTransactionCategoryType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add category type
        DB::table('transaction_category_type')->insert(
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
        $transferCategoryType = DB::table('transaction_category_type')
            ->select('id')
            ->where(['name' => 'transfer'])
            ->first()
        ;

        if (!empty($transferCategoryType)) {
            DB::table('transaction_category_type')->delete($transferCategoryType->id);
        }
    }
}
