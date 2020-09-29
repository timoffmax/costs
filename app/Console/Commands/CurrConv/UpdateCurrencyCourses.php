<?php
declare(strict_types=1);

namespace App\Console\Commands\CurrConv;

use App\Currency;
use App\Models\Service\Api\CurrConv\Request\Convert;
use Illuminate\Console\Command;

/**
 * Updates course for 'currency' table records
 * @see Currency
 */
class UpdateCurrencyCourses extends Command
{
    /**
     * @inheritdoc
     */
    protected $signature = 'currconv:refresh';

    /**
     * @inheritdoc
     */
    protected $description = "Updates course for 'currency' table records. Uses CurrConv API.";

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(Convert $convert): void
    {
        $convertTo = 'UAH'; // TODO: Move to user settings
        $currencies = Currency::all();

        foreach ($currencies as $currency) {
            $convertFrom = $currency->code;
            $convertResult = $convert->execute($convertFrom, $convertTo);

            $currency->course = $convertResult->getVal();
            $currency->save();
        }
    }
}
