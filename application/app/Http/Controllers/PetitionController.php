<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\PetitionData;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Petition;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;


class PetitionController extends Controller
{
    /**
     * Display the petition list.
     */

    public function index(Request $request)
    {
        $petitions = Petition::query()->get();
        $signedPetitions = Petition::whereRelation('signatures', 'user_id', $request->user()?->id)
            ->get();

        return Inertia::render('Petition/Index', [
            'petitions' => $petitions,
            'signedPetitions' => $signedPetitions,
        ]);
    }

    /**
     * Display a single petition.
     */
    public function view(Request $request, Petition $petition): Response
    {
        return Inertia::render('Petition/View', [
            'petition' => PetitionData::from($petition->load(['categories', 'user']))
        ]);
    }

    public function manage(Petition $petition)
    {
        $response = Gate::inspect('view', [Petition::class]);

        if ($response->allowed()) {
            return Inertia::render('Petition/Manage', [
                'petition' => $petition,
            ]);
        } else {
            return to_route('petitions.index');
        }
    }


}
