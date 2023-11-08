<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use App\Models\RecipeDetail;
use Exception;
use stdClass;

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

                $recipes = $this->parseRecipesBookmarkState($recipes, $userData);

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

                $recipes = $this->parseRecipesBookmarkState($recipes, $userData);

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

    private function parseRecipesBookmarkState($recipes, $userData) {        
        foreach ($recipes as $recipe) {            
            $recipe = $this->parseRecipeBookmarkState($recipe, $userData);
        }

        return $recipes;
    }

    private function parseRecipeBookmarkState($recipe, $userData) {
        $db = $this->supabaseService->initializeDatabase('favourites', 'id');
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
                    $recipe->bookmarked = true;    
                    break;
                }
            }
        }

        $recipe->totalLikes = count($result);
        return $recipe;
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

    public function saveRecipe(Request $request, $recipeId)
    {
        try {
            $auth = $this->supabaseService->createAuth();
            $bearerToken = $request->bearerToken();
            $userData = $bearerToken === 'undefined' ? null : $auth->getUser($bearerToken);
                
            if ($userData === null || $userData->aud !== 'authenticated') {
                return response()->json([
                    'type' => 'failure',
                    'data' => [
                        'error' => 'Please login to save recipes'
                    ]
                ], 401);
            }

            $db = $this->supabaseService->initializeDatabase('favourites', 'id');
            $query = [
                'select' => '*',
                'from'   => 'favourites',
                'where' =>
                [                    
                    'user_id' => 'eq.' . $userData->id,
                    'recipe_id' => 'eq.' . $recipeId
                ]
            ];

            $favourite = $db->createCustomQuery($query)->getResult();
            if (count($favourite) > 0) {
                $db->delete($favourite[0]->id);
            } else {
                $db->insert([
                    'user_id' => $userData->id,
                    'recipe_id' => $recipeId
                ]);
            }

            $recipe = new Recipe();
            $recipe->id = $recipeId;
            $recipe->bookmarked = false;
            $recipe->totalLikes = 0;
            
            return response()->json([
                'type' => 'success',
                'data' => [
                    'recipe' => $this->parseRecipeBookmarkState($recipe, $userData)
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
}
