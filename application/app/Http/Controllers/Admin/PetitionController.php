<?php

namespace App\Http\Controllers\Admin;

use App\Models\Rule;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Ballot;
use App\Models\Petition;
use App\Models\Question;
use App\Enums\RuleV1Enum;
use Illuminate\Http\Request;
use App\Enums\QuestionTypeEnum;
use App\Enums\RuleOperatorEnum;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\DataTransferObjects\RuleData;
use App\DataTransferObjects\BallotData;
use Illuminate\Support\Facades\Redirect;
use App\DataTransferObjects\PetitionData;

class PetitionController extends Controller
{
    /**
     * Display the petition list.
     */
    public function index()
    {
        $petitions = Petition::with('ballot')->get();
        $crumbs = [

            [
                'label' => 'Petitions',
                'link' => route('admin.petitions.index')
            ],
        ];

        return Inertia::render(
            'Auth/Petition/Index',
            [
                'petitions' => $petitions,
                'crumbs' => $crumbs,
            ]
        );
    }

    /**
     * Display a single petition.
     */
    public function view(Petition $petition): Response
    {
        return Inertia::render('Auth/Petition/View', [
            'petition' => PetitionData::from($petition->load(['rules', 'user'])),
            'crumbs' => [
                ['label' => 'Petitions', 'link' => route('admin.petitions.index')],
                ['label' => $petition->title],
            ]
        ]);
    }

    public function edit(Petition $petition): Response
    {
        return Inertia::render('Auth/Petition/Edit', [
            'petition' => PetitionData::from($petition->load(['rules', 'user', 'ballot'])),
            'crumbs' => [
                ['label' => 'Petitions', 'link' => route('admin.petitions.index')],
                ['label' => $petition->title],
            ]
        ]);
    }

    public function petitionsData(Request $request)
    {
        $page = $request->query('page') ?? 1;
        $perPage = $request->query('perPage') ?? 6;

        $petitions = Petition::paginate($perPage, ['*'], 'page', $page);
        return PetitionData::collection($petitions);
    }

    public function update(Request $request, Petition $petition, Ballot $ballot,)
    {
        $ballotEligible = intval($petition->petition_goals['ballot-eligible']['value2']);
        $operator = $petition->petition_goals['ballot-eligible']['operator'];
        $canMoveToBallot = compare_values($petition->signatures_count, $ballotEligible, $operator);

        switch ($request->status) {
            case 'approved':
                $petition->update([
                    'status' => $request->status
                ]);
                break;
            case 'ballot':
                DB::beginTransaction();
                try {
                    // Create ballot
                    if (!$canMoveToBallot) {
                        return back();
                    }

                    if (!$ballot?->id) {
                        $ballot = new Ballot;
                        $ballot->title = $request->ballotTitle;
                        $ballot->description = $request->ballotDescription;
                        $ballot->user_id = $petition->user_id;
                        $ballot->save();
                    }

                    $question = $ballot->questions()->first();

                    if (!$question?->id) {
                        // create question
                        $question = $ballot->questions()->create([
                            'title' => $request->questionTitle,
                            'status' => 'draft',
                            'type' => QuestionTypeEnum::SINGLE->value,
                            'user_id' => $petition->user_id,
                            'model_type' => Ballot::class,
                        ]);
                    }

                    // add choice to question
                    $question->choices()->create([
                        'title' => $petition->title,
                        'user_id' => $petition->user_id,
                        'description' => $petition->description,
                        'order' => $question->choices()->count() + 1
                    ]);

                    $ballot->petition()->save($petition);
                    DB::commit();
                    return BallotData::from($ballot);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    throw $th;
                    return back();
                }

                break;
        }
    }

    public function saveRule(Request $request, Petition $petition)
    {
        $request->validate([
            'type' => 'required',
            'v1' => 'required',
            'v2' => 'required',
            'title' => 'required'
        ]);

        $rule = $petition->rules()->where([
            'type' => $request->type,
            'value1' => $request->v1
        ])->first();

        if ($rule instanceof Rule) {
            $rule->value2 = $request->v2;
            $rule->save();
            return RuleData::from($rule);
        } else {
            $rule = new Rule;
            $rule->type = $request->type;
            $rule->title = $request->title;
            $rule->value1 = $request->v1;
            $rule->operator = RuleOperatorEnum::EQUALS_OR_GREATER_THAN->value;
            $rule->save();
            $petition->rules()->attach($rule->id);
            return RuleData::from($rule);
        }
    }

    public function removeRule(Petition $petition, Rule $rule)
    {
        $petition->rules()->detach($rule->id);
        $rule->delete();
        return to_route('petitions.manage', [
            'petition' => $petition->hash,
        ]);
    }

    public function moveToBallot(Petition $petition)
    {
        return Inertia::modal('Auth/Petition/CreateSelectBallot', [
            'petition' => PetitionData::from($petition->load(['rules', 'user']))
        ])->baseRoute(previous_route_name(), ['petition' => $petition->hash]);
    }
}
