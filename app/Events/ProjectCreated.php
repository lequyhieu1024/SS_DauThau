<?php

namespace App\Events;

use App\Models\UserNotification;
use App\Repositories\StaffRepository;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProjectCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $project;
    public $staffRepository;

    /**
     * Create a new event instance.
     */
    public function __construct($project)
    {
        $this->project = $project;
        $this->staffRepository = new StaffRepository();

        $this->content = "Dự án <b>{$this->project->name}</b> đã được tạo và đang chờ được bạn phê duyệt";
        UserNotification::create([
            'user_id' => $this->getUserId($this->project->staff_id),
            'project_id' => $this->project->id,
            'content' => $this->content
        ]);
    }

    public function getUserId($staff_id)
    {
        return $this->staffRepository->findOrFail($staff_id)->user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel("bidding-septenary.{$this->getUserId($this->project->staff_id)}")];
//        return new Channel("bidding-septenary");
    }
    public function broadcastAs()
    {
        return 'project-created';
    }
}
