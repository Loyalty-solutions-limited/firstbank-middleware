<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Events\NotificationSending; 
use App\Models\NotificationLog;

class NotificationSendingListener implements ShouldQueue
{
    public $tries = 5;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function backoff()
    {
        return [1, 5, 10,15,20];
    }
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(NotificationSending $event)
    {
        NotificationLog::create($event->log);
    }
}
