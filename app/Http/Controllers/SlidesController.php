<?php

namespace App\Http\Controllers;

use App\Event;
use App\Sponsor;
use Carbon\Carbon;
use App\Http\Requests;

class SlidesController extends Controller
{
    public function slides()
    {
        /**
         * @var $event Event
         */
        $event = (new Event)->nextEvent()->first();

        /**
         * @var $startTime Carbon
         */
        $startTime = $event->starts_at->copy();
        $startTime->setTimezone(new \DateTimeZone('Australia/Melbourne'));

        $nextEventStart = $event->followingEvent()
            ->starts_at->copy()->setTimeZone(new \DateTimeZone('Australia/Melbourne'));
        $nextHackStart = $event->followingHacknight()
            ->starts_at->copy()->setTimeZone(new \DateTimeZone('Australia/Melbourne'));

        $tsFilter = function ($key, $value) {
            return $key->name !== 'Teamsquare';
        };

        $sponsor = $event->sponsors->filter($tsFilter)->first();

        $sponsorHost = null;
        if ($startTime->month >= 8) {
            $sponsorHost = Sponsor::find(7);
        }

        $view = view('slides', [
            'sponsor' => $sponsor,
            'host' => $sponsorHost,
            'number' => $event->eventNumber(),
            'dateString' => $startTime->format('j F, Y'),
            'timezone' => $startTime->format('T'),
            'nextHacknight' => $event->followingHacknight(),
            'nextHackDateString' => $nextHackStart->format('j F, Y'),
            'nextEvent' => $event->followingEvent(),
            'nextEventDateString' => $nextEventStart->format('j F, Y')
        ])->render();

        return response($view, 200, ['Content-Type' => 'text/plain']);
    }
}
