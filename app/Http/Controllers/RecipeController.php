<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
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

    public function getRecipeDetail(RecipeDetail $recipeDetail)
    {
        dd($recipeDetail);
    }

    public function search(Request $request)
    {
        if ($request->type === 'ingredients') {
            if (!$request->has('ingredients')) {
                return response()->json([
                    'type' => 'failure',
                    'data' => [
                        'error' => 'Please enter at least one ingredient'
                    ]
                ], 401);
            }

            $recipes = Recipe::where([
                'type' => $request->type,
                'ingredients' => $request->ingredients
            ])->get();

            // TODO: Override likes with actual likes from DB
            foreach ($recipes as $recipe) {
                $recipe->likes = 0;
            }

            return response()->json([
                'type' => 'success',
                'data' => [
                    'recipes' => $recipes,
                ]
            ], 200);
        } else {
            if (!$request->has('title') || $request->title === '') {
                return response()->json([
                    'type' => 'failure',
                    'data' => [
                        'error' => 'Please enter a keyword'
                    ]
                ], 401);
            }

            $recipes = Recipe::where([
                'type' => $request->type,
                'query' => $request->title
            ])->get();

            // TODO: Override likes with actual likes from DB
            foreach ($recipes as $recipe) {
                $recipe->likes = 0;
            }

            return response()->json([
                'type' => 'success',
                'data' => [
                    'recipes' => $recipes['results'],
                ]
            ], 200);
        }
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
