<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RecipeDetail;
use App\Models\Spoonacular;
use App\Wrappers\SpoonacularWrapper;
use Cristal\ApiWrapper\Transports\Transport;
use Curl\Curl;

class RecipeController extends Controller
{
    public function getFavourites()
    {
        return response()->json([
            'type' => 'success',
            'data' => [
                'message' => 'RecipeController'
            ]
        ], 200);
    }

    public function getRecipeDetail(RecipeDetail $recipeDetail) {
        dd($recipeDetail);
    }    

    public function search(Request $request)
    {
        return response()->json([
            'type' => 'success',
            'data' => [
                'message' => 'RecipeController'
            ]
        ], 200);
    }

    public function save(Request $request)
    {
        return response()->json([
            'type' => 'success',
            'data' => [
                'message' => 'RecipeController'
            ]
        ], 200);
    }
}
