<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Territory extends Model{
	public $table = 'territories';
	public $timestamps = false;

	protected $fillable = [
		'name','startx', 'starty', 'endx', 'endy'
	];

	public function squares(){
      return $this->hasMany('App\Models\Square', 'id_territory', 'id');
    }

}