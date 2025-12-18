<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contact;
use App\Services\SmsService;
use App\Services\LoggerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendBirthdayMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthday:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send birthday messages to contacts celebrating today';

    /**
     * Execute the console command.
     */
    public function handle(SmsService $smsService)
    {
        $today = Carbon::today();
        $currentYear = $today->year;

        $this->info("Checking for birthdays on " . $today->format('Y-m-d'));
        LoggerService::log('Scheduler Started', "Running birthday check for " . $today->format('Y-m-d'));

        // SQLite doesn't have MONTH() and DAY() functions like MySQL by default in all versions or configs,
        // but Laravel's whereMonth and whereDay abstraction usually handles it.
        // If it fails with SQLite, will fallback to raw strftime query.
        $contacts = Contact::whereMonth('dob', $today->month)
                           ->whereDay('dob', $today->day)
                           ->get();

        $count = 0;
        foreach ($contacts as $contact) {
            if ($contact->last_message_sent_year == $currentYear) {
                $this->info("Skipping {$contact->name} (already sent this year)");
                continue;
            }

            if (!$contact->public_token) {
                $contact->public_token = \Illuminate\Support\Str::random(10);
                $contact->save();
            }

            $url = route('birthday.show', $contact->public_token);
            $name = strtoupper($contact->name);
            $message = "HAPPY BIRTHDAY {$name}!\n\nSITC Campus wishes you a year filled with success, knowledge and new opportunities.\n\nYour Birthday Card: {$url}\n\nKeep learning, growing and shining bright!";
            
            $this->info("Sending message to {$contact->name} ({$contact->phone})...");

            if ($smsService->send($contact->phone, $message)) {
                $contact->update(['last_message_sent_year' => $currentYear]);
                $this->info("Message sent successfully.");
                $count++;
            } else {
                $this->error("Failed to send message to {$contact->name}.");
            }
        }

        $this->info("Birthday messages sent: {$count}");
        LoggerService::log('Scheduler Completed', "Birthday check finished. Sent {$count} messages.", 'success', ['count' => $count]);
    }
}
