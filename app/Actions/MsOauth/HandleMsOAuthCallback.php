<?php

namespace App\Actions\MsOauth;

use App\Models\MicrosoftOAuth;
use App\Models\User;
use App\Services\MicrosoftGraphService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model\User as MSUser;

class HandleMsOAuthCallback
{
    /**
     * @throws GuzzleException
     * @throws GraphException
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function execute(User $user, ?string $authCode, ?string $providedState, ?string $expectedState): MicrosoftOAuth
    {
        $this->validate($authCode, $expectedState, $providedState);

        $accessToken = $this->getAccessToken($authCode);

        $msUser = $this->getMsUser($user);

        return $this->storeToken($accessToken, $msUser, $user);
    }

    /**
     * @throws Exception
     */
    private function validate(?string $authCode, ?string $expectedState, ?string $providedState): void
    {
        if (!isset($authCode)) {
            throw new Exception('No auth code provided');
        }

        if (!isset($expectedState)) {
            throw new Exception('No expected state provided');
        }

        if (!isset($providedState) || $expectedState != $providedState) {
            throw new Exception('State does not match');
        }
    }

    private function storeToken(AccessTokenInterface $accessToken, mixed $msUser, User $user): MicrosoftOAuth
    {
        return $user->msOauth()->create([
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'tokenExpires' => $accessToken->getExpires(),
            'userName' => $msUser->getDisplayName(),
            'userEmail' => $msUser->getMail() !== null ?: $msUser->getUserPrincipalName(),
            'userTimeZone' => $msUser->getMailboxSettings()->getTimeZone(),
        ]);
    }

    /**
     * @throws IdentityProviderException
     */
    private function getAccessToken(string $authCode): AccessToken|AccessTokenInterface
    {
        return MicrosoftGraphService::getOAuthClient()
            ->getAccessToken('authorization_code', [
                'code' => $authCode,
            ]);
    }

    /**
     * @throws GuzzleException
     * @throws GraphException
     */
    private function getMsUser($accessToken): mixed
    {
        return (new Graph())
            ->setAccessToken($accessToken->getToken())
            ->createRequest('GET', '/me?$select=displayName,mail,mailboxSettings,userPrincipalName')
            ->setReturnType(MSUser::class)
            ->execute();
    }
}

