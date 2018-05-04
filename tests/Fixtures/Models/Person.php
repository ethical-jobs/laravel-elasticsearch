<?php

namespace Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use EthicalJobs\Elasticsearch\Indexable;
use EthicalJobs\Elasticsearch\Document;

class Person extends Model implements Indexable
{
    use SoftDeletes, Document;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'first_name',    
        'last_name',     
        'age',
        'email',         
    ];    

    public function family()
    {
        return $this->belongsTo(Family::class);
    }     

    public function getDocumentRelations()
    {
        return ['family'];
    }      
}