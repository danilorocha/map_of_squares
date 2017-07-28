<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Territory;
use App\Models\Square;

class TerritoryController extends Controller{

	public function index(){
		$territories = Territory::get();
		$output = array();
		$output['count'] = $territories->count();

		$serializedData = array();
		foreach ($territories as $key=>$territory) {
			$serializedData[$key] = $this->serializeData($territory); 
		}
		$output['data'] = $serializedData;

		return response()->json($output, 200);
	}

	public function show($id, Request $request){		
		$territory = Territory::find($id);
		if(!$territory){
			return response()->json(['error'=>"territories/not-found: this territory was not found"], 404);
		}

		$data = $this->serializeData($territory);

		$inputs = $request->all();
		if(isset($inputs['withpainted']) && $inputs['withpainted'] == true){
			$serializedSquares = array();
			$squares = Square::where('id_territory', $data->id)->where('painted', 1)->get();

			foreach ($squares as $square) {
				$serializedSquares[] = array("x"=>$square->x, "y"=>$square->y);
			}
			$data->paited_squares = $serializedSquares;
		}

		return response()->json(['data'=>$data, 'error'=>false], 200);
	}

	public function store(Request $request){
		$validator = $this->requestValidator($request);

		if($validator->fails()){
			return response()->json(['error'=>"territories/incomplete-data: it misses the start, end or name field!"], 422);
		}
		
		$inputs = $this->allInputs($request);
		
		if($inputs == 'error'){
			return response()->json(['error'=>"territories/incomplete-data: it misses start or end point"], 422);
		}

		if($this->territoryOverlay($request['start'], $request['end'])){
			return response()->json(['error'=>"territories/territory-overlay: this new territory overlays another territory"], 409);
		}

		$territory = Territory::create($inputs);
		
		$square = array();
		$square['id_territory'] = $territory->id;

		for($i = $territory->starty; $i <= $territory->endy -1; $i++){
			for($j = $territory->startx; $j <= $territory->endx -1; $j++){
				$square['x'] = $j;
				$square['y'] = $i;
				Square::create($square);
			}
		}

		$data = $this->serializeData($territory);

		return response()->json([
			'data'=>$data, 'error'=>false	
		], 201);
	}

	public function destroy($id){
		$territory = Territory::find($id);
		if(!$territory){
			return response()->json(['error'=>"territories/not-found: this territory was not found"], 404);
		}

		$territory->delete();
		return response()->json(['error'=>false], 200);
	}

	public function requestValidator($request) {
		$validator = Validator::make($request->all(), [
			'name' => 'required',
            'start' => 'required',
            'end' => 'required'
		]);

		return $validator;
	}

	public function allInputs($request){
        $inputs = $request->all();
        if(isset($inputs['start']['x']) && isset($inputs['start']['y'])){
            $inputs['startx'] = $inputs['start']['x'];
            $inputs['starty'] = $inputs['start']['y'];
         	unset($inputs['start']);
        }else{
        	return 'error';
        }
        if(isset($inputs['end']['x']) && isset($inputs['end']['y'])){
            $inputs['endx'] = $inputs['end']['x'];
            $inputs['endy'] = $inputs['end']['y'];
            unset($inputs['end']);
        }else{
        	return 'error';
        }

        return $inputs;
    }

    public function serializeData($territory){
    	$territory->start = array('x'=>$territory->startx, 'y'=>$territory->starty);
    	$territory->end = array('x'=>$territory->endx, 'y'=>$territory->endy);
    	unset($territory->startx, $territory->starty, $territory->endx, $territory->endy);
		
		$territory->area = $this->calculateArea($territory->start, $territory->end);
		$territory->painted_area = Square::where('id_territory', $territory->id)->where('painted', 1)->count();
    	
    	return $territory;
    }

    public function calculateArea($start, $end){
    	$area = ($end['y'] - $start['y']) * ($end['x'] - $start['x']);
    	return $area;
    }

    public function territoryOverlay($start, $end){
    	$territories = Territory::get();
    	$aux = 0;
    	foreach ($territories as $territory) {

    		if($start['x'] >= $territory['endx'] xor $territory['startx'] >= $end['x'] ){
    			$aux++;
    		}elseif($start['y'] >= $territory['endy'] xor $territory['starty'] >= $end['y'] ){
    			$aux++;
    		}
    	}

    	if($aux == $territories->count()){
    		return false;
    	}
    	return true;
    }


}