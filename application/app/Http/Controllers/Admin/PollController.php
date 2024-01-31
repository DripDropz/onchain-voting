<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Poll;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\DataTransferObjects\PollData;

class PollController extends Controller
{
    /**
     * Display the poll list.
     */
    public function index()
    {
        $polls = Poll::all();
        $crumbs = [

            [
                'label' => 'Polls',
                'link' => route('admin.polls.index')
            ],
        ];

        return Inertia::render(
            'Auth/Poll/Index',
            [
                'polls' => $polls,
                'crumbs' => $crumbs,
            ]
        );
    }
    
    public function pollsData(Request $request)
    {
        $page = $request->query('page',1);
        $perPage = $request->query('perPage', 6);

        $polls = Poll::with('question.choices')->paginate($perPage, ['*'], 'page', $page);

        return PollData::collection($polls);
    }
}
