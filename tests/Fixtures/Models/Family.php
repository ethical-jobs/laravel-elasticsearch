<?php

namespace Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use EthicalJobs\Elasticsearch\Indexable;
use EthicalJobs\Elasticsearch\Document;

class Family extends Model implements Indexable
{
    use Document;
    
    public function vehicles()
    {
        return $this->hasOne(Vehicle::class);
    }

    public function members()
    {
        return $this->hasOne(Person::class);
    }     

    public function getDocumentRelations()
    {
        return ['vehicles','members'];
    }        
}