<?php

namespace App\Jobs\MAIL;

use App\Mail\EmailMailable;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailData;

    /**
     * Create a new job instance.
     *
     * @param  array  $emailData
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Send the email using the EmailMailable
        Mail::send(new EmailMailable($this->emailData));

        // Retrieve the Elasticsearch helper
        $elasticsearchHelper = app()->make(ElasticsearchHelperInterface::class);

        // Store the email in Elasticsearch
        $email = $elasticsearchHelper->storeEmail(
            $this->emailData['body'],
            $this->emailData['subject'],
            $this->emailData['to']
        );

        // Get an instance of the Redis helper
        $redisHelper = app()->make(RedisHelperInterface::class);

        // Store the recent message in Redis
        $redisHelper->storeRecentMessage($email['_id'], $this->emailData);

    }
}
