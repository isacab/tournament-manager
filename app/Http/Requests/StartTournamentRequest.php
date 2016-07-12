<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class StartTournamentRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules =  [
            "pools" => "required|array|min:1|max:20",
        ];

        if(is_array($this->input('pools')))
        {
            foreach ($this->input('pools') as $i => $value) 
            {
                $rules = array_merge($rules, [
                    "pools.". $i. ".name" => "string",
                    "pools.". $i. ".competitors" => "required|array",
                ]);
            }
        }

        return $rules;
    }

    /*public function validator()
    {
        $v = Validator::make($this->input(), $this->rules(), $this->messages(), $this->attributes());

        $v->extend('competitors_belongs_to_tournament', function($attribute, $value, $parameters) {
            
            if(!is_array($attribute) || !isset($parameters[0]))
                return true;

            $tid = $parameters[0];

            // All competitor ids in a single level array
            $competitorIds = array_flatten(array_pluck($attribute, 'competitors'));

            // Remove null values
            $competitorIds = array_filter($competitorIds, function($val){
                return !is_null($val);
            });

            $countBelongsToTournament = DB::table('competitors')
                                            ->whereIn('id', $competitorIds)
                                            ->where('tournament_id', '=', $tid)
                                            ->count();

            return count($competitorIds) == $countBelongsToTournament;
        });

        return $v;
    }*/
}
