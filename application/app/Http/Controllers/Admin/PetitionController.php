<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Petition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
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
    public function view(Request $request, Petition $petition): Response
    {
        return Inertia::render('Auth/Petition/View', [
            'petition' => PetitionData::from($petition->load(['categories', 'user'])),
            'crumbs' => [
                ['label' => 'Petitions', 'link' => route('admin.petitions.index')],
                ['label' => $petition->title],
            ]
        ]);
    }

    public function edit(Request $request, Petition $petition): Response
    {
        return Inertia::render('Auth/Petition/Edit', [
            'petition' =>PetitionData::from($petition->load(['categories','user'])),
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

    public function update(Request $request,Petition $petition)
    {
        $petition->update([
            'status' => $request->status
        ]);

        return to_route('admin.petitions.view', $petition->hash);

    }
}
