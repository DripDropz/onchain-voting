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
use App\Models\QuestionChoice;
use App\Models\QuestionResponse;

class PollController extends Controller
{
    public $perPage;

    public $polls;

    public ?string $nextCursor = null;

    public int $offset = 4;

    public bool $hasMorePages;

    public bool|null $hasPending;

    public bool|null $hasAnswered;

    public bool|null $hasActive;

    public ?string $status = 'pending';


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

        $actions = [
            [
                'label' => Auth::check() ? 'Create poll' : ' Login to create poll',
                'link' => Auth::check() ? route('polls.create') : route('login.email')
            ],
        ];

        $counts = $this->pollsCount(request());

        return Inertia::render('Poll/Index', [
            'counts' => $counts,
            'user' => $user,
            'crumbs' => $crumbs,
            'actions' => $actions,
        ]);
    }

    /**
     * Display the poll creation form.
     */
    public function create()
    {
        $crumbs = [
            [
                'label' => 'Polls',
                'link' => route('polls.index')
            ],
            [
                'label' => 'Create Poll',
                'link' => route('polls.create')
            ],
        ];

        $actions = [
            [
                'label' => Auth::check() ? 'Create poll' : ' Login to create poll',
                'link' => Auth::check() ? route('polls.create') : route('login.email')
            ],
        ];
        return Inertia::render(
            'Poll/Create',
            compact('crumbs', 'actions')
        );
    }

    /**
     * Store a newly created poll in storage.
     */
    public function pollsData(Request $request)
    {
        $this->perPage = $request->query('perPage', 4);
        $this->nextCursor = $request->query('nextCursor', null);
        $this->hasMorePages = $request->query('hasMorePages', false);
        $this->status = $request->query('status', null);
        $this->hasPending = $request->query('hasPending', false);
        $this->hasAnswered = $request->query('hasAnswered', false);
        $this->hasActive = $request->query('hasActive', false);

        $pollCursor = Poll::latest()
            ->when($this->hasPending, function ($query) {
                $query->where('user_id', Auth::user()->id)
                ->whereIn('status', ['draft', 'pending']);
            })->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })->when($this->hasAnswered, function ($query) {
                $query->where([
                    'status' => 'published',
                ])->whereRelation('user_responses', 'user_id', Auth::user()->id);
            })->when($this->hasActive, function ($query) {
                $query->where('user_id', Auth::user()->id)
                    ->whereIn('status', ['published']);
            })->when($this->nextCursor, function ($query) {
                $query->cursorPaginate($this->perPage, ['*'], 'cursor', $this->nextCursor);
            })->cursorPaginate($this->perPage);

        return [
            'polls' => PollData::collection(
                collect($pollCursor->items())
                    ->each(fn($p) => $p->load('question.choices', 'user_responses'))
            ),
            'nextCursor' => $pollCursor->nextCursor()?->encode(),
            'hasMorePages' => $pollCursor->hasMorePages(),
        ];
    }

    public function pollData(Request $request)
    {
        $poll = $request->route('poll');

        $poll->load('question.choices', 'user_responses');

        $pollData = PollData::from($poll);

        return $pollData;
    }

    private function pollsCount(Request $request)
    {
        $user = Auth::user();

        $activeCount = Poll::where('user_id', $user?->id)
            ->where('status', 'published')
            ->count();

        $pendingCount = Poll::where('user_id', $user?->id)
            ->whereIn('status', ['draft', 'pending'])
            ->count();

        $answeredCount = Poll::where('status', 'published')
            ->whereRelation('user_responses', 'user_id', $user?->id)
            ->count();

        return [
            'activeCount' => $activeCount,
            'pendingCount' => $pendingCount,
            'answeredCount' => $answeredCount,
            'allCount' => Poll::where('status', 'published')->count()
        ];
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            // 'pollTitle' => 'required|string|max:255',
            'question' => 'required|string|max:255',
            'options' => 'required|array|min:1',
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

        $question->user_id = $user->id;
        $question->model_id = $poll->id;

        $question->save();

        foreach ($validatedData['options'] as $key => $choice) {
            $question->choices()->create([
                'title' => $choice,
                'order' => $key,
                'question_id' => $question->id
            ]);
        }

        return redirect()->route('polls.index');
    }


    public function storeQuestionResponse(Request $request)
    {
        $poll = $request->route('poll');
        $question = Question::byHash($request->questionHash);
        $choice = QuestionChoice::byHash($request->selectedChoiceHash);
        $user = Auth::user();

        $questionResponse = new QuestionResponse([
            'user_id' => $user->id,
            'model_type' => Poll::class,
            'model_id' => $poll->id,
            'question_id' => $question->id,
        ]);

        $questionResponse->save();

        $questionResponse->choices()->attach($choice->id);

        return redirect()->route('polls.index');
    }
}
