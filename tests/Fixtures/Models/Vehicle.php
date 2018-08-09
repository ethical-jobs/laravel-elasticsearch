<?php

namespace Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use EthicalJobs\Elasticsearch\Contracts\Indexable;
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

    public function getDocumentMappings()
    {
        return [
            'family_id' => ['type' => 'integer'],
            'year'      => ['type' => 'integer'],
            'model'     => ['type' => 'text'],
            'make'      => ['type' => 'text'],
        ];
    }              
}