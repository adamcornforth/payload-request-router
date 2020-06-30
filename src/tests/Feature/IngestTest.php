<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Mockery\Mock;
use Prophecy\Argument;
use Tests\TestCase;

class IngestTest extends TestCase
{
    /**
     * @dataProvider salesPayload
     * @param string $content
     */
    public function testSalesPayload(string $content)
    {
        // Service A: Must not receive payloads about Campaign B for security reasons
        Http::shouldReceive('post')->with(
            'http://service_a',
            \Mockery::any()
        )->once();

        // Service C: Must receive all payloads
        Http::shouldReceive('post')->with(
            'http://service_c',
            \Mockery::any()
        )->once();

        // Service B: Must receive payloads about sales only
        Http::shouldReceive('post')->with(
            'http://service_b',
            \Mockery::any()
        )->once();


        $response = $this->json('POST', '/api/ingest', json_decode($content, true));
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
    }

    /**
     * @dataProvider campaignBPayload
     * @param string $content
     */
    public function testCampaignBPayload(string $content)
    {
        // Service A: Must not receive payloads about Campaign B for security reasons
        Http::shouldReceive('post')->with(
            'http://service_a',
            \Mockery::any()
        )->never();

        // Service C: Must receive all payloads
        Http::shouldReceive('post')->with(
            'http://service_c',
            \Mockery::any()
        )->once();

        // Service B: Must receive payloads about sales only
        Http::shouldReceive('post')->with(
            'http://service_b',
            \Mockery::any()
        )->never();

        $response = $this->json('POST', '/api/ingest', json_decode($content, true));
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
    }

    public function salesPayload() {
        return [
            [
                '{
                    "name": "Michael Collier", "phone": "07707000000", "email": "bigmike@collier.com", "query_type": {
                    "id": 1234,
                    "title": "SALE MADE" },
                    "call_stats": { "id": 5678,
                    "length": "01:56:34",
                    "recording_url": "http://remote.system/recording/5678" },
                    "campaign": { "id": 1011,
                    "name": "Campaign A",
                    "description": "A lovely campaign for Michael" }
                }'
            ]
        ];
    }

    public function campaignBPayload() {
        return [
            [
                '{
                    "name": "Jimmy Joe",
                    "phone": "07707000001", "email": "bigjim@collier.com", "query_type": {
                    "id": 5678,
                    "title": "DECLINED OFFER" },
                    "call_stats": { "id": 1213,
                    "length": "00:56:43",
                    "recording_url": "http://remote.system/recording/1213" },
                    "campaign": { "id": 1516,
                    "name": "Campaign B",
                    "description": "A different campaign not for Michael" }
                }'
            ]
        ];
    }
}
