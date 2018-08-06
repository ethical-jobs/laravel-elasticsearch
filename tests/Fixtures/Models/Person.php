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
        'sex',
        'email',         
    ];    

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'full_name'
    ];    

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'age' => 'integer',
    ];    

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

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
            'first_name' => ['type' => 'keyword'],
            'last_name' => ['type' => 'keyword'],
            'full_name' => ['type' => 'text'],
            'age' => ['type' => 'integer'],
            'sex' => ['type' => 'keyword'],
            'email' => ['type' => 'keyword'],
        ];
    }         
}