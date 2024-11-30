<?php

namespace App\Jobs;

use App\Mail\ForgotPasswordMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendForgotPasswordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {

        $this->data = $data;
//        Log::error('JOB: ' .$this->data);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
//        Log::info('JOB handle: ' .$this->data);
        Mail::to($this->data['email'])
            ->send(new ForgotPasswordMail($this->data));
    }
}
