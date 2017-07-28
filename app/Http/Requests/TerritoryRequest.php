<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
//use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;

class TerritoryRequest extends FormRequest
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
        return [
            'name' => 'required',
            'start' => 'required',
            'end' => 'required'
        ];
    }

    public function messages(){
        return [
            'name.required' => 'A name is required',
            'start.required' => 'A start point is required',
            'end.required' => 'A end point is required'
        ];
    }

    public function all(){
        $inputs = parent::all();
        if(isset($inputs['start'])){
            $inputs['startx'] = $inputs['start']['x'];
            $inputs['starty'] = $inputs['start']['y'];
        }
        if(isset($inputs['end'])){
            $inputs['endx'] = $inputs['end']['x'];
            $inputs['endy'] = $inputs['end']['y'];
        }

        return $inputs;
    }
}
