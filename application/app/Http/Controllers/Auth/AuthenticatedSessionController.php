<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Integrations\Lucid\Requests\AuthenticateWallet;
use App\Http\Integrations\Lucid\WalletConnector;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showWalletLogin()
    {
        return Inertia::modal('Auth/WalletLogin', [
            'baseUrl' => previous_route_name()
        ])
            ->baseRoute(previous_route_name());
    }

    public function walletLogin(Request $request)
    {

        $authenticateWallet = new AuthenticateWallet;

        $authenticateWallet->body()->merge([
            'signature' => $request->get($request->signature),
            'key' => $request->get($request->key)
        ]);

        $connector = new WalletConnector;
        $response = $connector->send($authenticateWallet);
        dd($response);
        return $response;
    }
}
