<?php
declare(strict_types=1);

use App\AccountType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ModifyAccountType
 */
class ModifyAccountType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add new field
        Schema::table(AccountType::ENTITY_TABLE, function (Blueprint $table) {
            $table->string(AccountType::LABEL, 50)->after(AccountType::NAME);
        });

        // Add labels to existed types
        DB::table(AccountType::ENTITY_TABLE)->where(AccountType::NAME, 'credit card')
            ->update([AccountType::NAME => AccountType::TYPE_CARD, AccountType::LABEL => 'Credit Card']);

        DB::table(AccountType::ENTITY_TABLE)->where(AccountType::NAME, 'cash')
            ->update([AccountType::LABEL => 'Cash']);

        // Add new types
        DB::table(AccountType::ENTITY_TABLE)->insert(
            [
                [AccountType::NAME => AccountType::TYPE_MONEYBOX, AccountType::LABEL => 'Moneybox'],
                [AccountType::NAME => AccountType::TYPE_DEPOSIT, AccountType::LABEL => 'Deposit'],
                [AccountType::NAME => AccountType::TYPE_SAVING, AccountType::LABEL => 'Saving'],
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
        $moneyboxType = DB::table(AccountType::ENTITY_TABLE)
            ->select(AccountType::ID)
            ->where([AccountType::NAME => AccountType::TYPE_MONEYBOX])
            ->first();

        $depositType = DB::table(AccountType::ENTITY_TABLE)
            ->select(AccountType::ID)
            ->where([AccountType::NAME => AccountType::TYPE_DEPOSIT])
            ->first();

        $savingType = DB::table(AccountType::ENTITY_TABLE)
            ->select(AccountType::ID)
            ->where([AccountType::NAME => AccountType::TYPE_SAVING])
            ->first();

        if (!empty($moneyboxType)) {
            DB::table(AccountType::ENTITY_TABLE)->delete($moneyboxType->id);
        }

        if (!empty($depositType)) {
            DB::table(AccountType::ENTITY_TABLE)->delete($depositType->id);
        }

        if (!empty($savingType)) {
            DB::table(AccountType::ENTITY_TABLE)->delete($savingType->id);
        }

        // Delete the column
        Schema::table(AccountType::ENTITY_TABLE, function (Blueprint $table) {
            $table->dropColumn(AccountType::LABEL);
        });
    }
}
