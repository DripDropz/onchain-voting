<?php

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\PollData;
use App\Enums\QueryParams;
use App\Http\Controllers\Controller;
use App\Models\Poll;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PollController extends Controller
{
    protected int $currentPage;

    protected int $limit;

    protected ?string $statusFilter = null;

    /**
     * Display the poll list.
     */
    public function index(Request $request): Response
    {
        $this->setFilters($request);
        $polls = $this->pollsData($request);
        $crumbs = [
            [
                'label' => 'Polls', 'link' => route('admin.polls.index'),
            ],
        ];

        return Inertia::render('Auth/Poll/Index', [
            'polls' => $polls,
            'counts' => $this->pollsCount(),
            'crumbs' => $crumbs,
            'perPage' => $this->limit,
            'currentPage' => $this->currentPage,
            'filter' => [
                'status' => $this->statusFilter,
            ],
        ]);
    }

    protected function setFilters(Request $request): void
    {
        $this->limit = (int) ($request->input(QueryParams::PER_PAGE) ?? $request->input('perPage', 6));
        $this->currentPage = (int) ($request->input(QueryParams::PAGE) ?? $request->input('page', 1));
        $this->statusFilter = $request->input(QueryParams::STATUS) ?? $request->input('status');
    }

    public function pollsData(Request $request)
    {
        $this->setFilters($request);

        $polls = Poll::query()
            ->latest()
            ->with(['question.choices', 'rules', 'user'])
            ->when($this->statusFilter === 'review', function ($query) {
                $query->whereIn('status', ['pending']);
            })
            ->when($this->statusFilter === 'active', function ($query) {
                $query->whereIn('status', ['approved', 'published']);
            })
            ->paginate($this->limit, ['*'], 'page', $this->currentPage);

        return PollData::collection($polls);
    }

    public function update(Request $request, Poll $poll): void
    {
        $request->validate([
            'status' => ['required', 'in:approved,rejected'],
        ]);

        abort_if($poll->status->value !== 'pending', 422, 'Only pending polls can be reviewed.');

        $poll->update([
            'status' => $request->status,
        ]);
    }

    private function pollsCount(): array
    {
        return [
            'allPolls' => Poll::count(),
            'activeCount' => Poll::whereIn('status', ['approved', 'published'])->count(),
            'pendingCount' => Poll::where('status', 'pending')->count(),
        ];
    }
}
