<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use Inertia\Inertia;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Enums\QuestionTypeEnum;
use Illuminate\Pagination\Cursor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\DataTransferObjects\PollData;

class PollController extends Controller
{
    public $perPage;

    public $polls;

    public ?string $nextCursor = null;

    public int $offset = 4;

    public bool $hasMorePages;


    /**
     * Display the poll list.
     */
    public function index()
    {
        $user = Auth::user();
        $crumbs = [
            [
                'label' => 'Polls',
                'link' => route('polls.index')
            ],
        ];


        return Inertia::render('Poll/Index', [
            // 'polls' => Poll::inRandomOrder()->with('question.choices')->take(4)->get(),
            'user' => $user,
            'crumbs' => $crumbs,
        ]);
    }

    /**
     * Display the poll creation form.
     */
    public function create()
    {
        return Inertia::render('Poll/Create');
    }

    /**
     * Store a newly created poll in storage.
     */
    public function pollsData(Request $request)
    {
        $this->perPage = $request->query('perPage', 4);
        $this->nextCursor = $request->query('nextCursor', null);
        $this->hasMorePages = $request->query('hasMorePages', false);
        $pollCursor = Poll::latest()
            ->when($this->nextCursor, function ($query) {
                $query->cursorPaginate($this->perPage, ['*'], 'cursor', $this->nextCursor);
            })
            ->cursorPaginate($this->perPage);

        return [
            'polls' => PollData::collection(collect($pollCursor->items())->each(fn ($p) => $p->load('question.choices'))),
            'nextCursor' => $pollCursor->nextCursor()?->encode(),
            'hasMorePages' => $pollCursor->hasMorePages(),
        ];
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            // 'pollTitle' => 'required|string|max:255',
            'question' => 'required|string|max:255',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:255',
            'publishOnchain' => 'boolean',
        ]);

        $poll = new Poll([
            'user_id' => $user->id,
            'title' => $validatedData['question'],
            'publish_on_chain' => $validatedData['publishOnchain'],
        ]);

        $poll->save();

        $question = new Question([
            'title' => $validatedData['question'],
            'model_type' => Poll::class,
            'model_id' => $poll->id,
            'type' => QuestionTypeEnum::MULTIPLE->value
        ]);
        $question->save();

        foreach ($validatedData['options'] as $key => $choice) {
            // dd($key);
            $question->choices()->create([
                'title' => $choice,
                'order' => $key,
                'question_id' => $question->id
            ]);
        }

        return redirect()->route('polls.index');
    }
}
