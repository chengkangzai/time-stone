<?php

namespace App\Http\Controllers;

use App\Actions\MsOauth\HandleMsOAuthCallback;
use App\Actions\MsOauth\ReadyForMsSignIn;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Microsoft\Graph\Exception\GraphException;

class MSOauthController extends Controller
{
    public function signin(ReadyForMsSignIn $action): RedirectResponse
    {
        [$oauthClient, $authUrl] = $action->execute();

        Session::put(['oauthState' => $oauthClient->getState()]);

        return redirect()->away($authUrl);
    }

    public function callback(Request $request, HandleMsOAuthCallback $action): Redirector|RedirectResponse|Application
    {
        $expectedState = Session::get('oauthState');
        $request->session()->forget('oauthState');
        $providedState = $request->query('state');

        try {
            $action->execute(auth()->user(), $request->query('code'), $providedState, $expectedState);
        } catch (IdentityProviderException|GuzzleException|GraphException|\Exception $e) {
            return redirect()
                ->route('scheduleConfig.index')
                ->with('error', 'Error requesting access token')
                ->with('errorDetail', json_encode($e->getResponseBody()));
        }

        return redirect()
            ->route('scheduleConfig.index')
            ->with('error', $request->query('error'))
            ->with('errorDetail', $request->query('error_description'));
    }

    public function signout(): Redirector|Application|RedirectResponse
    {
        auth()->user()->msOauth()->delete();

        return redirect('/');
    }
}
