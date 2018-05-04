<?php

namespace Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use EthicalJobs\Elasticsearch\Indexable;
use EthicalJobs\Elasticsearch\Document;

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
}