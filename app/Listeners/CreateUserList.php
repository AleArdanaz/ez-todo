<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateUserList
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $currentWeekDayString = date('l');

        $list = $event->user->lists()->create([
            'name' => $currentWeekDayString,
            'order' => 1,
        ]);

        $list->tasks()->create([
            'name' => 'Go to the gym',
            'completed' => false,
            'completed_at' => null,
            'due_date' => null,
            'remind_at' => null,
            'notes' => null,
            'priority' => 1,
        ]);

    }
}
