<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
//use LaravelLocalization;

class SaveRequest extends FormRequest
{
    public function authorize(){
        return true;
    }

    public function rules(){
        if($this->edit == "false"){
            return[
                "name" => "required|unique:food,name",
                "category" => "required",
            ];
        }
        else{
            return[
                "name" => "required",
                "category" => "required",
            ];
        }
    }

    public function messages(){
       // if(LaravelLocalization::getCurrentLocale() == "en"){
            //Validation messages in english
            return[
                "name.required" => "Veuillez entrer un nom",
                "name.unique" => "L'article que vous tentez d'ajouter existe déjà",
                "category.required" => "Veuillez choisir une catégorie",
            ];
        /*}

        else if(LaravelLocalization::getCurrentLocale() == "fr"){
            //Validation messages in french
            return[
                "fileUpload.required" => "Veuillez télécharger un fichier",
                "fileUpload.file" => "Veuillez télécharger un fichier",
                "fileUpload.mimes" => "Veuillez télécharger un fichier CSV valide seulement",
            ];
        }*/
    }
}
        