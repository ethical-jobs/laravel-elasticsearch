<?php

namespace Tests\Fixtures\Models;

use EthicalJobs\Elasticsearch\Contracts\Indexable;
use EthicalJobs\Elasticsearch\Document;
use Illuminate\Database\Eloquent\Model;

class Family extends Model implements Indexable
{
    use Document;

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class);
    }

    public function members()
    {
        return $this->hasMany(Person::class);
    }

    public function getDocumentRelations()
    {
        return ['vehicle', 'members'];
    }

    public function getDocumentMappings()
    {
        return [
            'surname' => ['type' => 'text'],
            'members' => ['type' => 'object'],
        ];
    }
}