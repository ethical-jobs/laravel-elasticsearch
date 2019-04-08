<?php

namespace EthicalJobs\Elasticsearch\Indexing;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Indexes queued documents
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */
class IndexDocuments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Indexable documents
     *
     * @var Collection
     */
    public $documents;

    /**
     * Create a new job instance.
     *
     * @param Collection $documents
     * @return void
     */
    public function __construct(Collection $documents)
    {
        $this->documents = $documents;
    }

    /**
     * Execute the job.
     *
     * @param Indexer $indexer
     * @return void
     */
    public function handle(Indexer $indexer): void
    {
        $indexer->indexCollection($this->documents);
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        $indexable = get_class($this->documents->first()) ?? '';

        return [
            'es',
            'es:indexing',
            'es:indexing:' . $indexable,
        ];
    }
}