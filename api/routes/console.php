<?php

use App\Models\Member;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule::call(function () {
//     $member = Member::find(1);
//     $member->contributions()->create();
// })->yearlyOn(12, 31, '22:00');
