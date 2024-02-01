<?php

namespace App\Http\Controllers\Admin;

use App\Enums\QuestionTypeEnum;
use App\Models\Ballot;
use App\Models\Rule;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Petition;
use App\Enums\RuleV1Enum;
use Illuminate\Http\Request;
use App\Enums\RuleOperatorEnum;
use App\Http\Controllers\Controller;
use App\DataTransferObjects\PetitionData;

class PetitionController extends Controller
{
    /**
     * Display the petition list.
     */
    public function index()
    {
        $petitions = Petition::all();
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
            'petition' => PetitionData::from($petition->load(['rules', 'user'])),
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

    public function update(Request $request, Petition $petition)
    {
        switch ($request->status) {
            case 'approve':
                $petition->update([
                    'status' => $request->status
                ]);
                break;
            case 'ballot':
                // Create ballot
                $ballot = new Ballot;
                $ballot->title = "Petition Ballot";
                $ballot->version = 1;
                $ballot->status = 'draft';
                $ballot->type = 'snapshot';
                $ballot->user_id = $petition->user_id;
                $ballot->save();

                // add petition as a choice on ballot

                // create question
                $question = $ballot->questions()->create([
                    'title' => 'Add a title',
                    'status' => 'draft',
                    'type' => QuestionTypeEnum::SINGLE->value,
                    'user_id' => $petition->user_id,
                    'model_type' => Ballot::class,
                ]);

                // add choice to question
                $question->choices()->create([
                    'title' => $petition->title,
                    'user_id' => $petition->user_id,
                    'description' => $petition->description,
                    'order' => $question->choices()->count() + 1
                ]);
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
}
