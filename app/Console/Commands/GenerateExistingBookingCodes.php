<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class GenerateExistingBookingCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:generate-codes {--force : Force regenerate all codes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate booking codes for existing bookings that don\'t have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        
        if ($force) {
            $this->warn('⚠️  FORCE MODE: Will regenerate ALL booking codes!');
            if (!$this->confirm('Are you sure?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
            
            $bookings = Booking::all();
            $message = 'Regenerating ALL booking codes...';
        } else {
            $bookings = Booking::whereNull('booking_code')->get();
            $message = 'Generating codes for bookings without code...';
        }

        if ($bookings->isEmpty()) {
            $this->info('✓ No bookings need code generation.');
            return 0;
        }

        $this->info($message);
        $bar = $this->output->createProgressBar($bookings->count());
        $bar->start();

        $generated = 0;
        $failed = 0;

        foreach ($bookings as $booking) {
            try {
                $booking->booking_code = Booking::generateUniqueBookingCode();
                $booking->save();
                $generated++;
            } catch (\Exception $e) {
                $this->error("\nFailed for Booking ID {$booking->id}: {$e->getMessage()}");
                $failed++;
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✓ Successfully generated: {$generated} codes");
        if ($failed > 0) {
            $this->error("✗ Failed: {$failed} bookings");
        }

        // Show sample codes
        $this->newLine();
        $this->info('Sample generated codes:');
        $samples = Booking::whereNotNull('booking_code')
            ->latest()
            ->limit(5)
            ->get(['id', 'booking_code', 'destination']);

        $this->table(
            ['ID', 'Booking Code', 'Destination'],
            $samples->map(fn($b) => [$b->id, $b->booking_code, $b->destination])
        );

        return 0;
    }
}