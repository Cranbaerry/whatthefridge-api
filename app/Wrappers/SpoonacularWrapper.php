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
}

