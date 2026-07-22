<?php

namespace App\Services\Api;

use Laravel\Passport\PersonalAccessTokenFactory;
use Laravel\Passport\PersonalAccessTokenResult;


class CustomPersonalAccessTokenFactory extends PersonalAccessTokenFactory
{
    public function make($userId, $name, array $scopes = [], array $customClaims = [])
    {
        $response = $this->dispatchRequestToAuthorizationServer(
            $this->createRequest($this->clients->personalAccessClient(), $userId, $scopes)
        );

        $token = tap($this->findAccessToken($response), function ($token) use ($userId, $name) {
            $this->tokens->save($token->forceFill([
                'user_id' => $userId,
                'name' => $name,
            ]));
        });

        // Add custom claims to the access token if provided
        if (!empty($customClaims)) {
            foreach ($customClaims as $key => $value) {
                $token->{$key} = $value;
            }
        }

        return new PersonalAccessTokenResult(
            $response['access_token'],
            $token
        );
    }
}
