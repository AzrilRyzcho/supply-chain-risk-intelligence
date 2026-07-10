<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CountryService;

class SyncCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'countries:sync {--force : Force updating existing country records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all countries from REST Countries API into local database';

    /**
     * Execute the console command.
     */
    public function handle(CountryService $countryService): int
    {
        $this->info('Starting countries synchronization from REST Countries API...');
        
        $force = $this->option('force');
        
        try {
            $synced = $countryService->syncAllCountries($force);
            $this->info('Successfully synchronized ' . count($synced) . ' countries from REST Countries API.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to sync countries: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
