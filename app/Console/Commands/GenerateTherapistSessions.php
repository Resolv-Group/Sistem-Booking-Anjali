<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TherapistSchedule;
use App\Services\SessionGenerator;

class GenerateTherapistSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-therapist-sessions {--days=30 : The number of days ahead to generate sessions for}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Generate therapist sessions for active schedules for the next N days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $this->info("Generating therapist sessions for the next {$days} days...");

        $activeSchedules = TherapistSchedule::where('status', 'Aktif')->get();

        if ($activeSchedules->isEmpty()) {
            $this->warn('No active therapist schedules found.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($activeSchedules->count());
        $bar->start();

        foreach ($activeSchedules as $schedule) {
            SessionGenerator::generate($schedule, $days);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Therapist sessions generated successfully!');

        return Command::SUCCESS;
    }
}
