<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPSupabase\Service;
use \GuzzleHttp\Exception\RequestException;

class AuthController extends Controller
{
    private $service;
    public function __construct()
    {
        $this->service = new Service(
            env('SUPABASE_ANON_KEY'),
            env('SUPABASE_URL')
        );        
    }

    public function login(Request $request)
    {
        $auth = $this->service->createAuth();
        $credentials = $request->only('email', 'password');

        try {
            $auth->signInWithEmailAndPassword($credentials['email'], $credentials['password']);
            $data = $auth->data();

            if (isset($data->access_token)) {
                $userData = $data->user; //get the user data
                return response()->json(['user' => $userData, 'token' => $data->access_token], 200);
            }
        } catch (RequestException $e) {
            return response()->json(['error' => $auth->getError()], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    public function register(Request $request)
    {
        $auth = $this->service->createAuth();
        $credentials = $request->only('email', 'password');

        try {
            $auth->createUserWithEmailAndPassword($credentials['email'], $credentials['password']);
            $data = $auth->data();

            if (isset($data->access_token)) {
                $userData = $data->user; //get the user data
                return response()->json(['user' => $userData, 'token' => $data->access_token], 200);
            }
        } catch (RequestException $e) {
            return response()->json(['error' => $auth->getError()], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
