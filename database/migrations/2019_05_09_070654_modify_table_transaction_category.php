<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ModifyTableTransactionCategory
 */
class ModifyTableTransactionCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $transferType = DB::table('transaction_type')
            ->select('id')
            ->where(['name' => 'transfer'])
            ->first()
        ;

        $transferCategory = DB::table('transaction_category_type')
            ->select('id')
            ->where(['name' => 'transfer'])
            ->first()
        ;

        if (!is_null($transferType) && !is_null($transferCategory)) {
            // Insert transfer categories
            DB::table('transaction_category')->insert(
                [
                    [
                        'transaction_type_id' => $transferType->id,
                        'type_id' => $transferCategory->id,
                        'name' => 'transfer from account'
                    ],
                    [
                        'transaction_type_id' => $transferType->id,
                        'type_id' => $transferCategory->id,
                        'name' => 'transfer to account'
                    ],
                    [
                        'transaction_type_id' => $transferType->id,
                        'type_id' => $transferCategory->id,
                        'name' => 'transfer fee'
                    ],
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $transferFromCategory = DB::table('transaction_category')
            ->select('id')
            ->where(['name' => 'transfer from account'])
            ->first()
        ;

        if (!empty($transferFromCategory)) {
            DB::table('transaction_category')->delete($transferFromCategory->id);
        }

        $transferToCategory = DB::table('transaction_category')
            ->select('id')
            ->where(['name' => 'transfer to account'])
            ->first()
        ;

        if (!empty($transferToCategory)) {
            DB::table('transaction_category')->delete($transferToCategory->id);
        }

        $transferFeeCategory = DB::table('transaction_category')
            ->select('id')
            ->where(['name' => 'transfer fee'])
            ->first()
        ;

        if (!empty($transferFeeCategory)) {
            DB::table('transaction_category')->delete($transferFeeCategory->id);
        }
    }
}
