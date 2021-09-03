<?php

namespace Tests\Feature;

use App\Http\Controllers\GlobalAPIController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalAPIControllerFeaturesTest extends TestCase
{
    public function classProvider()
    {
        $GlobalAPIController = new GlobalAPIController;
        $acceptedClasses = $GlobalAPIController->acceptedClasses;

        foreach ($acceptedClasses as $endPoint => $classPath) {
            $classProvider[] = [$endPoint];
        }

        return $classProvider;
    }

    /**
     * @dataProvider classProvider
     */
    public function test_get_a_response_from_each_endpoint($className)
    {
        $response = $this->get("/api/v1/{$className}");

        $response->assertOk();
        $response->assertJsonCount(13);
        $response->assertJsonStructure([
            'results' => []
        ]);
        $response->assertJsonFragment([
            'mainEndpoint' => $className, 
            'endpoint' => "api/v1/{$className}",
            'requestMethod' => "GET"
        ]);
        $res_array = (array)json_decode($response->content(), true);
        // just testing some key pieces
        $this->assertArrayHasKey('success', $res_array);
        $this->assertArrayHasKey('statusCode', $res_array);
        $this->assertArrayHasKey('errors', $res_array);
        $this->assertArrayHasKey('requestMethod', $res_array);
        $this->assertArrayHasKey('paramsSent', $res_array);
        $this->assertTrue(count($res_array['paramsSent']) == 3);
        $this->assertArrayHasKey('All', $res_array['paramsSent']);
        $this->assertArrayHasKey('GET', $res_array['paramsSent']);
        $this->assertArrayHasKey('POST', $res_array['paramsSent']);
        $this->assertArrayHasKey('paramsAccepted', $res_array);
        $this->assertArrayHasKey('paramsRejected', $res_array);
        $this->assertArrayHasKey('mainEndpoint', $res_array);
        $this->assertArrayHasKey('endpoint', $res_array);
        $this->assertArrayHasKey('endpointUrl', $res_array);
        $this->assertArrayHasKey('pageInfo', $res_array);
        $this->assertTrue(count($res_array['pageInfo']) == 3);
        $this->assertArrayHasKey('currentPage', $res_array['pageInfo']);
        $this->assertArrayHasKey('totalPages', $res_array['pageInfo']);
        $this->assertArrayHasKey('requestPerPage', $res_array['pageInfo']);
        $this->assertArrayHasKey('totalResults', $res_array);
        $this->assertArrayHasKey('results', $res_array);
        // TODO: add all end point once done, need to add more???

    }

    public function test_get_a_error_response_from_notAnEndpoint()
    {
        $response = $this->get("/api/v1/notAnEndpoint");

        $response->assertStatus(404);
    }

    public function test_get_a_error_response_from_all_other_paths()
    {
        $response = $this->get("api/v1/notAnEndpoint/5435/subNotAnEndpoint/63453/other/text/yep");

        $response->assertStatus(404);
    }
}
