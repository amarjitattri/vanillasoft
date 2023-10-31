<?php

namespace App\Services;

use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redis;

class RedisHelper implements RedisHelperInterface
{
    /**
     * Store a recent message in Redis.
     */
    public function storeRecentMessage(mixed $id, array $emails): void
    {
        // Encrypt the email data and convert it to a JSON string
        $token = Crypt::encrypt(json_encode($emails));

        // Store the encrypted token in Redis with the provided ID
        Redis::set($id, json_encode($token));
    }
}
