<?php

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\PollData;
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

    protected ?string $sortBy = null;

    protected ?string $sortOrder = null;

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
            'sort' => [
                'sortBy' => $this->sortBy ?? 'created_at',
                'sortOrder' => $this->sortOrder ?? 'desc',
            ],
        ]);
    }

    protected function setFilters(Request $request): void
    {
        $this->limit = (int) ($request->input('perPage', 10));
        $this->currentPage = (int) ($request->input('page', 1));

        // Default to 'review' (pending) status to show only records awaiting approval
        $this->statusFilter = $request->input('status', 'review');
        $this->sortBy = $request->input('sortBy', 'created_at');
        $this->sortOrder = $request->input('sortOrder', 'desc');
    }

    public function pollsData(Request $request)
    {
        $this->setFilters($request);

        $polls = Poll::query()
            ->with(['question.choices', 'rules', 'user']);

        // Apply status filter - default to review (pending/approved)
        if ($this->statusFilter === 'review' || $this->statusFilter === null) {
            $polls = $polls->whereIn('status', ['pending', 'approved']);
        } elseif ($this->statusFilter === 'active') {
            $polls = $polls->whereIn('status', ['approved', 'published']);
        } elseif ($this->statusFilter === 'all') {
            // No status filter - show all
        } else {
            // Specific status filter
            $polls = $polls->where('status', $this->statusFilter);
        }

        // Apply sorting
        $sortBy = $this->sortBy ?? 'created_at';
        $sortOrder = $this->sortOrder ?? 'desc';

        if ($sortBy === 'title') {
            $polls = $polls->orderBy('title', $sortOrder);
        } elseif ($sortBy === 'status') {
            $polls = $polls->orderBy('status', $sortOrder);
        } else {
            // Default to created_at
            $polls = $polls->orderBy('created_at', $sortOrder);
        }

        return PollData::collection($polls->paginate($this->limit, ['*'], 'page', $this->currentPage));
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
