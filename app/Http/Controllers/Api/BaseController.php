<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\Passport;
use League\OAuth2\Server\ResourceServer;
use Illuminate\Http\Request;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class BaseController extends Controller
{



    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }



    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }


    public function makePaginator($data): array
    {
        return [
            'data' => $data,
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
            'links' => [
                'next' => $data->nextPageUrl(),
                'prev' => $data->previousPageUrl(),
            ],
        ];
    }

    public function decodeToken(Request $request)
    {
        try {
            // Retrieve the Bearer token
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['status' => 'error', 'message' => 'Token not provided'], 400);
            }

            // Convert Illuminate Request to PSR-7 Request
            $psr17Factory = new Psr17Factory();
            $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
            $psrRequest = $psrHttpFactory->createRequest($request);

            // Parse the token using ResourceServer
            $resourceServer = app(ResourceServer::class);
            $parsedToken = $resourceServer->validateAuthenticatedRequest($psrRequest);

            // Get token information
            $tokenId = $parsedToken->getAttribute('oauth_access_token_id');
            $tokenRepository = app(TokenRepository::class);
            $accessToken = $tokenRepository->find($tokenId);

            if (!$accessToken) {
                return response()->json(['status' => 'error', 'message' => 'Token not found'], 404);
            }

            // Fetch all token information
            $user = $accessToken->user;
            $client = $accessToken->client;
            $scopes = $accessToken->scopes;
            $customer_id = $accessToken->customer_id;
            $business_id = $accessToken->business_id;

            return [
                'status' => 'success',
                'token_id' => $tokenId,
                'user' => $user,
                'client' => $client,
                'scopes' => $scopes,
                'expires_at' => $accessToken->expires_at,
                'customer_id' => $customer_id,
                'business_id' => $business_id,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

}
