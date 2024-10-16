<?php

namespace App\Jobs;

use App\Enums\ProjectStatus;
use App\Events\UpdateProjectStatusEvent;
use App\Repositories\ProjectRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateProjectStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $projectRepository;

    /**
     * Create a new job instance.
     */
    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $projects = $this->projectRepository->getOverdueProjectSubmission();
        if (!empty($projects)) {
            foreach ($projects as $project) {
                $project->status = ProjectStatus::SELECTING_CONTRUCTOR->value;
                $project->save();
                event(new UpdateProjectStatusEvent($project));
            }
        }
    }
}
