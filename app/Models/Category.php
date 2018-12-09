<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';
    protected $primaryKey = "id_category";
    public $timestamps = false;

    public function foods(){
    	return $this->hasMany("App\Models\Food", "id_category", "id_category");
    }
}
