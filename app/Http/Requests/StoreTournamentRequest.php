<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class StoreTournamentRequest extends Request
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
        $rules = [
            "name"              => "required",
            "stages"            => "array|max:5",
            "competitors"       => "array|max:100",
        ];

        if(is_array($this->input('stages')))
            foreach ($this->input('stages') as $i => $value) {
                array_merge($rules, [
                    "stages.". $i. ".name" => "required|string|max:255",
                    "stages.". $i. ".type" => "required|in:SingleElimination,RoundRobin",
                ]);
            }

        if(is_array($this->input('competitors')))
            foreach ($this->input('competitors') as $i => $value) {
                array_merge($rules, [
                    "competitors.". $i. ".name" => "required|string|min:1|max:255"
                ]);
            }

        return $rules;
    }
}
