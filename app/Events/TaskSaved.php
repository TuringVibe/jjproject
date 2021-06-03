<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $project_id;
    public $task_id;
    public $old_status;
    public $new_status;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($project_id, $task_id, $old_status, $new_status)
    {
        $this->project_id = $project_id;
        $this->task_id = $task_id;
        $this->old_status = $old_status;
        $this->new_status = $new_status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
