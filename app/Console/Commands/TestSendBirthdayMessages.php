<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contact;
use Carbon\Carbon;

class TestSendBirthdayMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthday:test-send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear last sent year for today\'s birthdays and force send messages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        
        $this->info("Clearing last_message_sent_year for contacts with birthday on " . $today->format('Y-m-d'));

        $contacts = Contact::whereMonth('dob', $today->month)
                           ->whereDay('dob', $today->day)
                           ->get();

        $count = 0;
        foreach ($contacts as $contact) {
            $contact->update(['last_message_sent_year' => null]);
            $count++;
        }

        $this->info("Cleared history for {$count} contacts.");

        $this->info("Triggering birthday:send command...");
        $this->call('birthday:send');
    }
}
