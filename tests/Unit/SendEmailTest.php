<?php

namespace Tests\Jobs\MAIL;

use App\Jobs\MAIL\SendEmail;
use App\Mail\EmailMailable;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendEmailTest extends TestCase
{
    public function testHandleMethod()
    {
        // Mock the email data
        $emailData = [
            'to' => 'test@test.com',
            'subject' => 'Test Subject',
            'body' => 'Test Body',
        ];

        // Mock the ElasticsearchHelperInterface and RedisHelperInterface
        $elasticsearchHelper = $this->mock(ElasticsearchHelperInterface::class);
        $redisHelper = $this->mock(RedisHelperInterface::class);

        // Expectations for ElasticsearchHelperInterface
        $elasticsearchHelper->shouldReceive('storeEmail')
            ->once()
            ->with(
                $emailData['body'],
                $emailData['subject'],
                $emailData['to']
            )
            ->andReturn(['_id' => 'some_id']); // Mock the returned data

        $redisHelper->shouldReceive('storeRecentMessage');

        // Mock the Mail facade to prevent actual email sending
        Mail::fake();

        // Create a job instance
        $job = new SendEmail($emailData);

        // Execute the job's handle method
        $job->handle();

        // Assertions
        Mail::assertSent(EmailMailable::class, function ($mail) {
            return true;
        });

        // Verify that the Elasticsearch and Redis operations were called as expected
        $elasticsearchHelper->shouldHaveReceived('storeEmail');
        $redisHelper->shouldHaveReceived('storeRecentMessage');
    }
}
