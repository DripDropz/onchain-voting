<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Integrations\Lucid\Requests\AuthenticateWallet;
use App\Http\Integrations\Lucid\LucidConnector;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;

class WalletLoginController extends Controller
{
    public function __construct(
        public AuthenticateWallet $authenticateWallet,
        public LucidConnector $connector
    ){}

    public function showWalletLogin(Request $request)
    {
        if (!! $request->hash) {
            $baseUrl = url()->previous();
            $baseRoute = previous_route_name();
        } else {
            $baseUrl = '/';
            $baseRoute = 'home';
        }

        return Inertia::modal('Auth/WalletLogin', [
            'baseUrl' => $baseUrl,
        ])
        ->baseRoute($baseRoute, $request->hash);
    }

    public function signMessageLogin(Request $request)
    {
        $this->authenticateWallet->body()->merge([
            'signature' => $request->get('signature'),
            'key' => $request->get('key'),
        ]);

        $response = $this->connector->send($this->authenticateWallet);

        if ($response == $request->stakeAddrHex) {
            return $this->authenticate($request);
        }

        return Redirect::back()->withErrors(['error' => 'Credentials mismatch']);
    }

    public function txLogin(Request $request)
    {
        $this->authenticateWallet = new AuthenticateWallet;

        $this->authenticateWallet->body()->merge([
            'stakeAddr' => $request->get('stakeAddr'),
            'txHash' => $request->get('txHash'),
        ]);

        $response = $this->connector->send($this->authenticateWallet);

        if ($response) {
            return $this->authenticate($request);
        }

        return Redirect::back()->withErrors(['error' => 'Credentials mismatch']);

    }

    public function authenticate($request)
    {
        $user = User::where('voter_id', $request->stakeAddr)->first();

        if ($user instanceof User) {
            Auth::login($user);

            return redirect($request->redirect);
        }

        $password = Str::random(8);
        $user = User::create([
            'name' => $request->stakeAddr,
            'voter_id' => $request->stakeAddr,
            'password' => Hash::make($password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect($request->redirect);
    }
}
