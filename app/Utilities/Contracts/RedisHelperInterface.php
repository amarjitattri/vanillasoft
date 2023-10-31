<?php

namespace App\Utilities\Contracts;

interface RedisHelperInterface
{
    /**
     * Store the id of a message along with a message subject in Redis.
     *
     * @param  string  $messageSubject
     * @param  string  $toEmailAddress
     */
    public function storeRecentMessage(mixed $id, array $emails): void;
}
