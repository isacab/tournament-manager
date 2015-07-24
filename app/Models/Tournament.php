<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tournaments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'privacy'];

    public function competitors()
    {
        return $this->hasMany('App\Models\Competitor');
    }

    public function competitorCount()
    {
        return $this->competitors()
                ->selectRaw('tournament_id, count(*) as competitorCount')
                ->groupBy('tournament_id');
    }

    public function stages()
    {
        return $this->hasMany('App\Models\Stage', 'tournament_id'); 
    }

    public function pools()
    {
        return $this->hasManyThroug('App\Models\Pools', 'App\Models\Stages', 'tournament_id', 'stage_id');
    }

    public function currentStage()
    {
        return $this->stages()->where('status', '=', 'InProgress');
    }

    public function nextStage()
    {
        return $this->stages()->where('status', '=', 'NotStarted');
    }

    public function previousStage()
    {
        return $this->stages()->where('status', '=', 'Finished')->orderBy('id', 'DESC');
    }

}
