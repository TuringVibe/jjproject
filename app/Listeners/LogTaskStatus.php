<?php

namespace App\Listeners;

use App\Events\TaskSaved;
use App\Models\TaskStatusLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LogTaskStatus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TaskSaved  $event
     * @return void
     */
    public function handle(TaskSaved $event)
    {
        $user_id = 0;
        if($event->old_status == null) {
            $notes = sprintf('Task created with status %s', $event->new_status);
        } else {
            $notes = sprintf('Task status changed from %s to %s', $event->old_status, $event->new_status);
        }
        if(Auth::check()) $user_id = Auth::user()->id;
        TaskStatusLog::create([
            'project_id' => $event->project_id,
            'task_id' => $event->task_id,
            'last_status' => $event->new_status,
            'notes' => $notes,
            'created_at' => Carbon::now(),
            'created_by' => $user_id
        ]);
    }
}
