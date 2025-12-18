<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Console\ClosureCommand;

// Run hourly to ensure messages are sent even if the server was down at midnight.
// The command handles duplicate checking internally, so this is safe.
// Schedule::command('birthday:send')->hourly();

