<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Elasticsearch;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmailControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_valid_email_data_can_be_queued()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        $token = $user->createToken('test-token')->plainTextToken;

        $data = [
            [
                'to' => 'test@test.com',
                'subject' => 'Test Subject',
                'body' => 'Test Body',
            ],
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Content-Type' => 'application/json',
        ])->postJson("/api/v1/{$user->user_name}/send", $data);

        $response->assertStatus(202);
    }

    public function test_exception_returns_500_response()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Arrange
        $data = [
            [
                'to' => 'invalid-email',
                'subject' => '',
                'body' => '',
            ],
        ];

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token, ])
            ->postJson("/api/v1/$user->user_name/send", $data);

        // Assert
        $response->assertStatus(500);
    }
    public function test_list_method_returns_view_with_elasticsearch_data()
    {
        // Mock the Elasticsearch service or use a testing Elasticsearch instance.
        // Replace this with your actual Elasticsearch data retrieval logic.

        // Simulate data retrieval from Elasticsearch (replace with your data)
        $mockElasticsearch = $this->mock(Elasticsearch::class);
        $mockElasticsearch->shouldReceive('getAllEmails')->andReturn(['email1@example.com', 'email2@example.com']);

        // Call the 'list' route
        $response = $this->get('/list');

        // Assert that the response is successful (HTTP 200) and contains a view
        $response->assertStatus(200);
        $response->assertViewIs('list.index');

    }

}
