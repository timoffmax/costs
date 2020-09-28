<?php
declare(strict_types=1);

use App\Currency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds fields needed for currency conversion
 */
class ModifyCurrencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function up()
    {
        $this->addNewFields();
        $this->setCurrencyCodes();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('currency', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('course');
            $table->dropColumn('course_updated_at');
        });
    }

    /**
     * Adds new fields to the table
     */
    private function addNewFields(): void
    {
        Schema::table('currency', function (Blueprint $table) {
            $table->string('code', 3)->nullable(false);
            $table->unsignedDecimal('course', 10, 4)->nullable(false)->default(1);
            $table->timestamp('course_updated_at')->nullable(false);
        });
    }

    /**
     * Fills newly created field 'code'
     */
    private function setCurrencyCodes(): void
    {
        /** @var Currency $uah **/
        $uah = Currency::where('name', 'Ukraine Hryvnia')
            ->firstOrFail();

        /** @var Currency $usd **/
        $usd = Currency::where('name', 'United States Dollar')
            ->firstOrFail();

        /** @var Currency $eur **/
        $eur = Currency::where('name', 'Euro')
            ->firstOrFail();

        /** @var Currency $rur **/
        $rur = Currency::where('name', 'Russia Ruble')
            ->firstOrFail();

        $uah->code = 'UAH';
        $uah->save();

        $usd->code = 'USD';
        $usd->save();

        $eur->code = 'EUR';
        $eur->save();

        $rur->code = 'RUR';
        $rur->save();
    }
}
