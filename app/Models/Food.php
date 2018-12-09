<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $table = 'food';
    protected $primaryKey = "id_food";
    public $timestamps = false;

    public function category(){
    	return $this->belongsTo("App\Models\Category", "id_category", "id_category");
    }
}
