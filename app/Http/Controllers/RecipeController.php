<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use App\Models\RecipeDetail;
use Exception;

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

    public function searchRecipes(Request $request)
    {
        try {
            $auth = $this->supabaseService->createAuth();
            $bearerToken = $request->bearerToken();
            $userData = $bearerToken === 'undefined' ? null : $auth->getUser($bearerToken);

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

                $recipes = $this->parseRecipeBookmarkState($recipes, $userData);

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

                $recipes = $this->parseRecipeBookmarkState($recipes, $userData);

                return response()->json([
                    'type' => 'success',
                    'data' => [
                        'recipes' => $recipes,
                    ]
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'type' => 'error',
                'data' => [
                    'error' => $e->getMessage()
                ]
            ], 500);
        }
    }

    private function parseRecipeBookmarkState($recipes, $userData) {
        $db = $this->supabaseService->initializeDatabase('favourites', 'id');
        foreach ($recipes as $recipe) {            
            $query = [
                'select' => '*',
                'from'   => 'favourites',
                'where' =>
                [
                    'recipe_id' => 'eq.' . $recipe->id
                ]
            ];

            $result = $db->createCustomQuery($query)->getResult();                    
            if ($userData !== null && $userData->aud === 'authenticated') {
                foreach ($result as $data) {
                    if ($userData->id === $data->user_id) {
                        $recipe->isLiked = true;    
                        break;
                    }
                }
            }
 
            $recipe->totalLikes = count($result);
        }

        return $recipes;
    }

    public function getRecipeBookmarks(Request $request, $recipeId)
    {
        try {
            $db = $this->supabaseService->initializeDatabase('favourites', 'id');
            $query = [
                'select' => '*',
                'from'   => 'favourites',
                'where' =>
                [
                    'recipe_id' => 'eq.' . $recipeId
                ]
            ];

            $result = $db->createCustomQuery($query)->getResult();
            return response()->json([
                'type' => 'success',
                'data' => [
                    'count' => count($result),
                    'rows' => $result
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'type' => 'error',
                'data' => [
                    'error' => $e->getMessage()
                ]
            ], 500);
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
