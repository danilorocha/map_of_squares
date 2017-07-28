<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Square;

class SquareController extends Controller{

	public function show($x, $y){

		$square = Square::where('x', $x)->where('y', $y)->first();
		if(!$square){
			return response()->json(['error'=>"squares/not-found: this square does not belong to any territory"], 404);
		}

		$data = $this->serializeData($square);
		return response()->json(['data'=>$data, 'error'=>false], 200);
	}

	public function update($x, $y, $paint){

		$square = Square::where('x', $x)->where('y', $y)->first();
		if(!$square){
			return response()->json(['error'=>"squares/not-found: this square does not belong to any territory"], 404);
		}

		if($paint){
			$square->painted = 1;
			$square->save();
		}

		$data = $this->serializeData($square);
		return response()->json(['data'=>$data, 'error'=>false], 200);
	}

	public function serializeData($square){
		unset($square->id);
		unset($square->id_territory);

		return $square;
	}
}