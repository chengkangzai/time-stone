<?php

namespace App\Actions\MsOauth;

use App\Services\MicrosoftGraphService;

class ReadyForMsSignIn
{
    public function execute(): array
    {
        $oauthClient = MicrosoftGraphService::getOAuthClient();

        $authUrl = $oauthClient->getAuthorizationUrl();

        return [$oauthClient, $authUrl];
    }
}
