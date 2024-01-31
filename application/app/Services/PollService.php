<?php

namespace App\Services;

use App\Models\Poll;
use Illuminate\Support\Collection;
use App\DataTransferObjects\PollData;

class PollService 
{
    public $perPage = 4;

    public  ?Collection $polls;

    public ?string $nextCursor = null;

    public int $offset = 0;

    public bool $hasMorePages;
    
    public function pollsData()
    {
        $pollCursor = Poll::with('question.choices')
        ->latest()
            ->offset($this->offset)
            ->cursorPaginate($this->perPage);

        $this->setNextCursor($pollCursor);
        $this->addNewPolls(collect($pollCursor->items()));

        return PollData::collection($this->polls);
    }

    public function loadMore()
    {
        dd($this->polls);
        $pollCursor = Poll::with('question.choices')->latest()
            ->latest()
            ->offset(4)
            ->cursorPaginate(
                $this->perPage,
                ['*'],
                'cursor',
                $this->nextCursor
            );
        $this->setNextCursor($pollCursor);
        $this->addNewPolls(collect($pollCursor->items()));

        return PollData::collection($pollCursor->items());
    }



    protected function addNewPolls($newPolls): void
    {
        // dd($newPolls, $this->polls);
        isset($this->polls)
            ? $this->polls = $this->polls->merge($newPolls)
            : $this->polls = $newPolls;
    }

    protected function setNextCursor($cursor): void
    {
        $this->hasMorePages = $cursor->hasMorePages();
        // dump($this->hasMorePages, $cursor->nextCursor()->encode());

        if ($this->hasMorePages) {
            $this->nextCursor = $cursor->nextCursor()->encode();
        }
    }
}
