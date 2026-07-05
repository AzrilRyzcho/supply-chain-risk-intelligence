<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RiskScoringService;

class CalculateRiskScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'risk:calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate risk scores for all countries using Weighted Risk Model';

    /**
     * Execute the console command.
     */
    public function handle(RiskScoringService $riskScoringService): int
    {
        $this->info('Starting risk scores calculation for all countries...');
        
        try {
            $riskScoringService->calculateAllCountries();
            $this->info('Risk scores successfully calculated and saved.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to calculate risk scores: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
