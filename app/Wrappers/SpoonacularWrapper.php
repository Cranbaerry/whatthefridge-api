<?php

namespace App\Wrappers;

use Cristal\ApiWrapper\Transports\TransportInterface;
use Cristal\ApiWrapper\Api;

class SpoonacularWrapper extends Api
{
    protected $transport, $apiKey;
    
    public function __construct(TransportInterface $transport)
    {
        $this->apiKey = env("SPOONACULAR_KEY", "somedefaultvalue");
        $this->transport = $transport;
    }

    public function getRecipeDetail($id)
    {  
        return $this->transport->request("recipes/{$id}/information?includeNutrition=false&apiKey={$this->apiKey}");
    }

    public function getRecipeDetails(array $filters)
    {  
     
        

        return $this->transport->request("recipes/{$filters}/information?se&apiKey={$this->apiKey}", $filters);
    }


    

    public function getRecipes(array $queryString)
    {
        $queryString['apiKey'] = $this->apiKey;
        if ($queryString['type'] == 'ingredients') {           
            // https://api.spoonacular.com/recipes/findByIngredients?ingredients=${ingredients}&number=20&ranking=2&ignorePantry=true&apiKey=${PRIVATE_SPOONACULAR_KEY}

            $queryString['ignorePantry'] = true;
            $queryString['number'] = 20;
            $queryString['ranking'] = 2;
            $queryString['ingredients'] = implode(',', $queryString['ingredients']);

            return $this->transport->request("recipes/findByIngredients", $queryString);
        } else {
            // https://api.spoonacular.com/recipes/complexSearch?query=${title}&number=20&addRecipeInformation=true&apiKey=${PRIVATE_SPOONACULAR_KEY}

            $queryString['number'] = 20;
            $queryString['addRecipeInformation'] = true;

            return $this->transport->request("recipes/complexSearch", $queryString);
        } 
    }
}

