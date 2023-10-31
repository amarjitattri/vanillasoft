<?php

namespace App\Http\Controllers;

use App\Jobs\MAIL\SendEmail;
use App\Models\User;
use App\Services\Elasticsearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    /**
     * Send email(s) for a user.
     */
    public function send(Request $request, string $user): \Illuminate\Http\JsonResponse
    {

        try {
            // Check if the user with the given username exists
            if (User::where('user_name', $user)->exists()) {

                // Validate the request data
                $validator = Validator::make($request->all(), [
                    'emails.*.to' => 'required|email',
                    'emails.*.subject' => 'required|string',
                    'emails.*.body' => 'required|string',
                ]);

                // Check for validation errors
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                // Dispatch SendEmail job for each email data
                foreach ($validator->getData() as $emailData) {
                    SendEmail::dispatch($emailData);
                }

                return response()->json(['message' => 'Emails have been queued for sending'], 202);

            } else {
                return response()->json([
                    'message' => 'user_name is not Found',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    /**
     * List emails using Elasticsearch.
     */
    public function list()
    {
        try {
            // Instantiate Elasticsearch service
            $elasticSearch = new Elasticsearch();
            // Get all emails from Elasticsearch
            $data = $elasticSearch->getAllEmails();

            if (count($data) > 0) {
                // Return a view with email data
                return view('list.index', compact('data'));
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 404);
        }

    }
}
