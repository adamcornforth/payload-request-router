<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class IngestController extends Controller
{
    /**
     * Ingest some payload and forward it onto another service
     * @param Request $request
     * @return JsonResponse
     */
    public function ingest(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse([
                'error' => json_last_error_msg(),
                'content' => $request->getContent()
            ], Response::HTTP_BAD_REQUEST);
        }

        /**
         * Todo:
         * Persist this payload
         *
         * Get all the routing rules from the database
         * Combine rules for Microservice if more than one rule exists for a microservice
         *
         * Iterate each Microservice's rules and query the payloads to see if any match
         *     If the payload matches, forward the payload to the matching microservice
         *
         * Delete the payload when processed
         */

        // A: Must receive all payloads
        // A: Must not receive payloads about campaign B
        if ($content['campaign']['name'] !== 'Campaign B')
            Http::post('http://service_a', $content);


        // C: Must receive all payloads
        Http::post('http://service_c', $content);

        // B: Must receive payloads about sales only
        if ($content['query_type']['title'] === 'SALE MADE')
            Http::post('http://service_b', $content);

        return new JsonResponse([]);
    }
}
