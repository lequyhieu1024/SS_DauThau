<?php

namespace App\Jobs;

use App\Mail\sendApproveProjectMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class sendApproveProjectJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $investor;
    protected $tenderer;
    protected $notes;

    protected $status;

    /**
     * Create a new job instance.
     */
    public function __construct($investor, $tenderer, $notes, $status)
    {
        $this->investor = $investor;
        $this->tenderer = $tenderer;
        $this->notes = $notes;
        $this->status = $status;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [
            'notes' => $this->notes,
            'status' => $this->status,
        ];

        // Gửi email cho investor
        $investorData = array_merge($data, ['user' => $this->investor]);
        Mail::to($this->investor['email'])
            ->send(new SendApproveProjectMail($investorData));

        // Gửi email cho tenderer
        $tendererData = array_merge($data, ['user' => $this->tenderer]);
        Mail::to($this->tenderer['email'])
            ->send(new SendApproveProjectMail($tendererData));
    }
}
