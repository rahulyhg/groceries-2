<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Models\Category;
use App\Models\Food;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }

    public function index(){
        $categories = Category::orderBy("priority")->get();
        $foodItems = Food::orderBy("name")->get();
        return view("index", compact("categories", "foodItems"));
    }

    public function saveNewItem(Request $request){
        if(Input::get("edit") == "true")
            $food = Food::find(Input::get("id_food"));
        else
            $food = new Food;
        $food->name = Input::get("name");
        $food->id_category = Input::get("category");
        if(Input::get("addToList") == "true"){
            $food->quantity = 1;
        }
        $food->save();
        return $food->id_food;
    }

    public function addToList(Request $request){
        $food = Food::find(Input::get("id_food"));
        if($food->quantity === null)
            $food->quantity = Input::get("amount");
        else{
            if(Input::get("amount") == 0){
                $food->quantity = null;
            }
            else{
                if(Input::get("adjust") == "true")
                    $food->quantity = Input::get("amount");
                else
                    $food->quantity += Input::get("amount");
            }
        }
        $food->save();
        $return = ["name"=>$food->name, "category"=>$food->id_category, "quantity"=>$food->quantity];
        return json_encode($return);
    }
}
