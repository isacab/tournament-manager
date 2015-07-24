<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pool extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'stage_id'];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function stage()
    {
        return $this->belongsTo('App\Models\Stage', 'stage_id'); 
    }

    public function matches()
    {
        return $this->hasMany('App\Models\Match');
    }

    public function standings()
    {
    	return $this->hasMany('App\Models\StandingsRecord')->orderBy('points', 'DESC')->orderBy('difference', 'DESC')->orderBy('for', 'DESC')->orderBy('name', 'ASC');
    }

    public function competitors()
    {
        return $this->belongsToMany('App\Models\Competitor', 'pool_members')->orderBy('pool_members.id');
    }
}
