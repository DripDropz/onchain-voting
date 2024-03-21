<?php

namespace App\Http\Controllers\Admin;

use App\Models\Rule;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Ballot;
use App\Models\Petition;
use App\Models\Question;
use App\Enums\RuleV1Enum;
use App\Enums\QueryParams;
use Illuminate\Http\Request;
use App\Enums\QuestionTypeEnum;
use App\Enums\RuleOperatorEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Stringable;
use App\Http\Controllers\Controller;
use App\DataTransferObjects\RuleData;
use App\DataTransferObjects\BallotData;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\DataTransferObjects\PetitionData;
use App\Events\PetitionSigned;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class PetitionController extends Controller
{

    public array|null $filter;

    protected int $currentPage;

    protected int $limit;

    protected null|string|Stringable $projectStatus = null;

    /**
     * Display the petition list.
     */
    public function index(Request $request)
    {
        $petitions = $this->petitionsData($request);

        $props = [
            'perPage' => $this->limit,
            'currentPage' => $this->currentPage,
            'filter' => [
                'status' => $this->projectStatus
            ],
            'counts' => $this->petitionsCount(request()),
            'petitions' => $petitions,
            'crumbs' => [
                'label' => 'Petitions',
                'link' => route('admin.petitions.index')
            ],
        ];

        return Inertia::render('Auth/Petition/Index', $props);
    }

    protected function setFilters(Request $request)
    {
        $this->limit = $request->input(QueryParams::PER_PAGE, 6);
        $this->currentPage = $request->input(QueryParams::PAGE, 1);

        $this->projectStatus = match ($request->input(QueryParams::STATUS, null)) {
            'r' => implode(',', ['pending', 'approved']),
            'a' => 'published',
            default => null
        };
    }

    private function petitionsCount(Request $request)
    {
        $allCount = Petition::with('status', ['published', 'pending', 'approved', 'draft', 'rejected'])->count();

        $activeCount = Petition::where('status', ['published'])->count();

        $pendingCount = Petition::whereIn('status', ['pending', 'approved'])->count();

        return [
            'allPetitions' => $allCount,
            'activeCount' => $activeCount,
            'pendingCount' => $pendingCount,
        ];
    }

    public function petitionsData(Request $request)
    {
        $this->setFilters($request);

        $petitions = Petition::latest();

        if ($this->projectStatus !== null) {
            $statuses = explode(',', $this->projectStatus);
            $petitions = $petitions->whereIn('status', $statuses);
        }

        $results = $petitions->paginate($this->limit, ['*'], 'p', $this->currentPage)
            ->onEachSide(1)
            ->toArray();

        return $results;
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
        $petition->load('media');

        return Inertia::render('Auth/Petition/Edit', [
            'petition' => PetitionData::from($petition->load(['rules'])),
            'petitionImg' => optional($petition->getMedia('petitions'))->first()?->getUrl(),
            'crumbs' => [
                ['label' => 'Petitions', 'link' => route('admin.petitions.index')],
                ['label' => $petition->title],
            ]
        ]);
    }


    public function update(Request $request, Petition $petition, Ballot $ballot,)
    {

        switch ($request->status) {
            case 'approved':
                $petition->update([
                    'status' => $request->status
                ]);
                break;
            case 'ballot':
                $ballotEligible = intval($petition->petition_goals['ballot-eligible']['value2']);
                $operator = $petition->petition_goals['ballot-eligible']['operator'];
                $canMoveToBallot = compare_values($petition->signatures_count, $ballotEligible, $operator);
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
            PetitionSigned::dispatch($petition);
            return RuleData::from($rule);
        } else {
            $rule = new Rule;
            $rule->type = $request->type;
            $rule->title = $request->title;
            $rule->value1 = $request->v1;
            $rule->operator = RuleOperatorEnum::EQUALS_OR_GREATER_THAN->value;
            $rule->save();
            $petition->rules()->attach($rule->id);
            PetitionSigned::dispatch($petition);
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

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function uploadImage(Request $request, Petition $petition)
    {
        $this->validate($request, [
            'key' => 'required|string',
            'filename' => 'required|string',
        ]);

        $key = $request->key;
        $filename = $request->filename;

        $media = $petition->addMediaFromDisk($key, config('filesystems.default'))
            ->addCustomHeaders([
                'ACL' => 'public-read'
            ])
            ->usingFileName($filename)
            ->toMediaCollection('petitions');

        $url = $media->getUrl();

        return response()->json(['url' => $url]);
    }
}
