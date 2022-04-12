<?php

namespace App\Services;

use App\Models\MicrosoftOAuth;
use App\Models\User;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use Microsoft\Graph\Graph;

class MicrosoftGraphService
{
    public static function getOAuthClient(): GenericProvider
    {
        return new GenericProvider([
            'clientId' => config('azure.appId'),
            'clientSecret' => config('azure.appSecret'),
            'redirectUri' => config('azure.redirectUri'),
            'urlAuthorize' => config('azure.authority') . config('azure.authorizeEndpoint'),
            'urlAccessToken' => config('azure.authority') . config('azure.tokenEndpoint'),
            'urlResourceOwnerDetails' => '',
            'scopes' => config('azure.scopes'),
        ]);
    }

    public static function getOrUpdateAccessToken(User $user)
    {
        /** @var MicrosoftOAuth $token */
        $token = $user->msOauth()->first();

        if (! $token->expired()) {
            return $token->accessToken;
        }

        try {
            $oauthClient = static::getOAuthClient();

            $newToken = $oauthClient->getAccessToken('refresh_token', [
                'refresh_token' => $token->refreshToken,
            ]);

            $user->msOauth()->update([
                'accessToken' => $newToken->getToken(),
                'refreshToken' => $newToken->getRefreshToken(),
                'tokenExpires' => $newToken->getExpires(),
            ]);

            return $newToken->getToken();
        } catch (IdentityProviderException) {
            return '';
        }
    }

    public static function getGraphWithUser(User $user): Graph
    {
        return (new Graph())->setAccessToken(static::getOrUpdateAccessToken($user));
    }
}
