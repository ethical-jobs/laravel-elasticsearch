<?php

namespace Tests\Fixtures\Models;

use EthicalJobs\Elasticsearch\Contracts\Indexable;
use EthicalJobs\Elasticsearch\Document;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model implements Indexable
{
    use Document;

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function getDocumentRelations()
    {
        return ['family'];
    }

    public function getDocumentMappings()
    {
        return [
            'family_id' => ['type' => 'integer'],
            'year' => ['type' => 'integer'],
            'model' => ['type' => 'text'],
            'make' => ['type' => 'text'],
        ];
    }
}