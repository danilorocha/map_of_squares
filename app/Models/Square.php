<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Square extends Model{
	public $table = 'squares';
	public $timestamps = false;

	protected $fillable = [
		'x','y', 'painted', 'id_territory'
	];


}