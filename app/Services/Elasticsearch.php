<?php

namespace App\Services;

use App\Utilities\Contracts\ElasticsearchHelperInterface;
use Illuminate\Support\Facades\Http;

class Elasticsearch implements ElasticsearchHelperInterface
{
    /**
     * Store an email in Elasticsearch.
     */
    public function storeEmail(string $messageBody, string $messageSubject, string $toEmailAddress): mixed
    {
        // Send a POST request to Elasticsearch to store the email
        $response = Http::post(config('elasticsearch.url').'/emails/_doc', [
            'email' => $toEmailAddress,
            'subject' => $messageSubject,
            'body' => $messageBody,
        ]);

        return $response->json();
    }

    /**
     * Get all emails from Elasticsearch.
     */
    public function getAllEmails(): array
{
    try {
        // Send a GET request to Elasticsearch to retrieve all emails
        $response = Http::get(config('elasticsearch.url').'/emails/_search');

        // Check if the request was successful (HTTP status 200)
        if ($response->successful()) {
            // Extract the email data from the response and return it as an array
            return $response->json()['hits']['hits'];
        } else {
            // Return an empty array when no data is found (HTTP status 404)
            return response()->json(['error' => 'Data not found'], 404);
        }
    } catch (\Exception $e) {
        // Handle exceptions and return an empty array (HTTP status 404) when an exception occurs
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}
