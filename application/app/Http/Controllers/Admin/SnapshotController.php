<?php

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\SnapshotData;
use App\DataTransferObjects\QuestionData;
use App\Enums\ModelStatusEnum;
use App\Enums\QuestionTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Snapshot;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use JetBrains\PhpStorm\NoReturn;
use Illuminate\Support\Facades\Gate;

class SnapshotController extends Controller
{
    /**
     * Display the new snapshots's form.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/Snapshot/Create', []);
    }

    /**
     * Display the snapshots's form.
     */
    public function view(Request $request, Snapshot $snapshot): Response
    {
        return Inertia::render('Auth/Snapshot/View', [
            'snapshot' => SnapshotData::from($snapshot),
        ]);
    }

    /**
     * Display the snapshots's form.
     */
    public function edit(Request $request, Snapshot $snapshot): Response
    {
        return Inertia::render('Auth/Snapshot/Edit', [
            'snapshot' => SnapshotData::from($snapshot)
        ]);
    }

    /**
     * Store a newly created Snapshot in storage.
     */
    public function store(SnapshotData $snapshotData): RedirectResponse
    {
        $response = Gate::inspect('create', Snapshot::class);

        if ($response->allowed()) {
            $snapshot = new Snapshot;
            $snapshot->fill($snapshotData->all());
            $snapshot->save();

            return Redirect::route('admin.snapshots.view', ['snapshot' => $snapshot->hash]);
        } else {
            return Redirect::back()->withErrors(['error' => 'Not authorized to create snapshots']);
        }
    }

    /**
     * Update the snapshots's profile information.
     */
    public function update(Request $request, Snapshot $snapshot)
    {
        $response = Gate::inspect('update', $snapshot);

        if ($response->allowed()) {
            $snapshot = Snapshot::byHash($snapshot->hash);
            $snapshot->title = $request->title;
            $snapshot->description = $request->description;
            $snapshot->status = $request->status;
            $snapshot->type = $request->type;
            $snapshot->policy_id = $request->policy_id;
            $snapshot->update();

            return Redirect::back();
        }else {
            return Redirect::back()->withErrors(['error' => 'Not authorized']);
        }
    }

    /**
     * Delete the snapshots's account.
     */
    public function destroy(Request $request, $snapshot): RedirectResponse
    {
        $response = Gate::inspect('delete', $snapshot);

        if ($response->allowed()) {
            $existingBallot = Snapshot::byHash($snapshot);
            $existingBallot->delete();

            return Redirect::to('/');
        } else {
            return Redirect::route('admin.snapshots.view', ['snapshots' => $snapshot]);
        }
    }

    public function createQuestion(Request $request, Snapshot $snapshot)
    {
        $response = Gate::inspect('create', Question::class);
        if ($response->allowed()) {
            return Inertia::modal('Auth/Question/Create')
                ->with([
                    'snapshots' => SnapshotData::from($snapshot),
                    'questionTypes' => QuestionTypeEnum::values(),
                    'questionsStatuses' => ModelStatusEnum::values(),
                ])
                ->baseRoute('admin.snapshots.edit', [
                    'snapshots' => $snapshot->hash
                ]);
        } else {
            return Redirect::back()->withErrors(['error' => 'Not authorized to create question']);
        }
    }

    public function editQuestion(Request $request, Snapshot $snapshot, Question $question)
    {
        $snapshot->load(['questions']);
        return Inertia::render('Auth/Question/Edit', [
            'snapshots' => SnapshotData::from($snapshot),
            'question' => QuestionData::from($question)
        ]);
    }

     /**
     * Store a newly created Question in storage.
     */
    #[NoReturn]
    public function updateQuestion(Request $request, $snapshot, $question): RedirectResponse
    {
        $response = Gate::inspect('update', $question);

        if ($response->allowed()) {
            $question = Question::byHash($question->hash);
            $question->title = $request->title;
            $question->description = $request->description;
            $question->status = $request->status;
            $question->type = $request->type;
            $question->max_choices = $request->maxChoices;
            $question->supplemental = $request->supplemental;
            $question->update();

            return Redirect::route('admin.snapshots.view', ['snapshots' => $snapshot->hash]);
        } else {
            return redirect()->route('admin.snapshots.view', ['snapshots' => $snapshot->hash])->withErrors([
                'error' => 'Not authorized to update this question!',
            ]);
        }

    }

    /**
     * Store a newly created Question in storage.
     */
    #[NoReturn]
    public function storeQuestion(Request $request, $snapshot): RedirectResponse
    {
        $response = Gate::inspect('create', Question::class);

        if ($response->allowed()) {
            $question = new Question;
            $question->title = $request->title;
            $question->description = $request->description;
            $question->status = $request->status;
            $question->type = $request->type;
            $question->max_choices = $request->maxChoices;
            $question->supplemental = $request->supplemental;
            $question->user_id = Auth::id();
            $question->ballot_id = decode_model_hash($snapshot?->hash, Snapshot::class);
            $question->save();

            return Redirect::route('admin.snapshots.view', ['snapshots' => $snapshot?->hash]);
        } else {
            return redirect()->route('admin.snapshots.view', ['snapshots' => $snapshot?->hash])->withErrors([
                'error' => 'Not authorized to create question!',
            ]);
        }
    }

     /**
     * Delete the snapshots's account.
     */
    public function destroyQuestion(Request $request,Snapshot $snapshot,Question $question)
    {
        $response = Gate::inspect('delete', $question);

        if ($response->allowed()) {
            $que = Question::where('id', $question->id)->first();
            $que->choices()->delete();
            $que->delete();
        } else {
            return Redirect::back()->withErrors(['error' => 'Not authorized to delete question']);
        }
    }
}
