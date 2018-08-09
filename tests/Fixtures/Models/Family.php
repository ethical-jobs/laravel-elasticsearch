<?php

namespace Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use EthicalJobs\Elasticsearch\Contracts\Indexable;
use EthicalJobs\Elasticsearch\Document;

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
        return ['vehicle','members'];
    }     

    public function getDocumentMappings()
    {
        return [
            'surname' => ['type' => 'text'],
            'members' => ['type' => 'object'],
        ];
    } 
}