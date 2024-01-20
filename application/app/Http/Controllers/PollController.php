<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Models\Poll;
use Illuminate\Support\Facades\Auth;

class PollController extends Controller
{
    /**
     * Display the poll list.
     */

    public function index()
    {
        $user = Auth::user();
        return Inertia::render('Poll/Index', [
            'polls' => Poll::query()->get(),
            'user' => $user,
        ]);
    }
}
