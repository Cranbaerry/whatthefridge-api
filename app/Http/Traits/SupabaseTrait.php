<?php
     
namespace App\Http\Traits;
use PHPSupabase\Service;

trait SupabaseTrait {
 
    public function initializeSupabaseService() 
    {
        return new Service(
            env('SUPABASE_ANON_KEY'),
            env('SUPABASE_URL')
        );        
    }

    public function getUriBase(string $suffix) : string
    {
        return env('SUPABASE_URL') . $suffix;
    }

    public function getGoogleAuthUrl() 
    {
        $url = $this->getUriBase('/auth/v1/authorize');
        $query = http_build_query([
            'provider' => 'google',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ]);

        return $url . '?' . $query;
    }

    public function getDiscordAuthUrl() 
    {
        $url = $this->getUriBase('/auth/v1/authorize');
        $query = http_build_query([
            'provider' => 'discord',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ]);

        return $url . '?' . $query;
    }
}