<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Console\ClosureCommand;

Schedule::command('birthday:send')->dailyAt('00:00');

