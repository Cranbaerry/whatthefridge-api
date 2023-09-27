<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
