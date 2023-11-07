<?php

namespace App\Models;

use App\Models\Spoonacular;

class Recipe extends Spoonacular
{
    // This property is directly used and pluralized by the API Wrapper (ex : getUsers).
    protected $entity = 'recipe';

    // If your API resource can be identified with a unique key you can define 
    // the primary key. By default it is 'id'.
    protected $primaryKey = 'id';
}