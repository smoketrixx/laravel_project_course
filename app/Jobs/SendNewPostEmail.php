<?php

namespace App\Jobs;

use App\Mail\NewPostEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendNewPostEmail implements ShouldQueue
{
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $incomingFields;

    /**
     * Create a new job instance.
     */
    public function __construct($incomingFields)
    {
        $this->incomingFields=$incomingFields;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->incomingFields['toUser'])->send(new NewPostEmail(['name' => $this->incomingFields['name'], 'title' => $this->incomingFields['title']] ));
    }
}
