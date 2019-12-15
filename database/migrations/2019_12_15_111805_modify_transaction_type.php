<?php
declare(strict_types=1);

use App\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class ModifyTransactionType
 */
class ModifyTransactionType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add new field
        Schema::table(TransactionType::ENTITY_TABLE, function (Blueprint $table) {
            $table->string(TransactionType::LABEL, 50)->after(TransactionType::NAME);
        });

        // Add labels to existed types
        DB::table(TransactionType::ENTITY_TABLE)->where(TransactionType::NAME, 'income')
            ->update([TransactionType::LABEL => 'Income']);

        DB::table(TransactionType::ENTITY_TABLE)->where(TransactionType::NAME, 'cost')
            ->update([TransactionType::LABEL => 'Cost']);

        DB::table(TransactionType::ENTITY_TABLE)->where(TransactionType::NAME, 'transfer')
            ->update([TransactionType::LABEL => 'Transfer']);

        // Add new types
        DB::table(TransactionType::ENTITY_TABLE)->insert(
            [
                [TransactionType::NAME => TransactionType::TYPE_MONEYBOX, TransactionType::LABEL => 'Recharge moneybox'],
                [TransactionType::NAME => TransactionType::TYPE_DEPOSIT, TransactionType::LABEL => 'Recharge deposit'],
                [TransactionType::NAME => TransactionType::TYPE_SAVING, TransactionType::LABEL => 'Recharge saving'],
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
        // Delete recently created types
        $moneyboxType = DB::table(TransactionType::ENTITY_TABLE)
            ->select(TransactionType::ID)
            ->where([TransactionType::NAME => TransactionType::TYPE_MONEYBOX])
            ->first();

        $depositType = DB::table(TransactionType::ENTITY_TABLE)
            ->select(TransactionType::ID)
            ->where([TransactionType::NAME => TransactionType::TYPE_DEPOSIT])
            ->first();

        $savingType = DB::table(TransactionType::ENTITY_TABLE)
            ->select(TransactionType::ID)
            ->where([TransactionType::NAME => TransactionType::TYPE_SAVING])
            ->first();

        if (!empty($moneyboxType)) {
            DB::table(TransactionType::ENTITY_TABLE)->delete($moneyboxType->id);
        }

        if (!empty($depositType)) {
            DB::table(TransactionType::ENTITY_TABLE)->delete($depositType->id);
        }

        if (!empty($savingType)) {
            DB::table(TransactionType::ENTITY_TABLE)->delete($savingType->id);
        }

        // Delete the column
        Schema::table(TransactionType::ENTITY_TABLE, function (Blueprint $table) {
            $table->dropColumn(TransactionType::LABEL);
        });
    }
}
