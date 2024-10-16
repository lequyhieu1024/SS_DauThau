<?php

namespace App\Console\Commands;

use App\Jobs\UpdateProjectStatusJob;
use App\Repositories\ProjectRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateProjectStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-project-status-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật trạng thái của các dự án đã hết thời gian nộp hồ sơ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        UpdateProjectStatusJob::dispatch(new ProjectRepository());
    }
}
