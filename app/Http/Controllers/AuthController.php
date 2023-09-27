<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\SupabaseTrait;
use \GuzzleHttp\Exception\RequestException;

class AuthController extends Controller
{
    use SupabaseTrait;
    private $service;
    public function __construct()
    {
        $this->service = $this->initializeSupabaseService();
    }

    public function login(Request $request)
    {
        $auth = $this->service->createAuth();
        $credentials = $request->only('email', 'password');

        try {
            $auth->signInWithEmailAndPassword($credentials['email'], $credentials['password']);
            $data = $auth->data();

            if (isset($data->access_token)) {
                $userData = (array) $data->user;
                return response()->json([
                    'type' => 'success',
                    'data' => [
                        'token' => $data->access_token,
                        'user' => $userData,
                        'login' => true
                    ]
                ], 200);
            }
        } catch (RequestException $e) {
            return response()->json([
                'type' => 'failure',
                'data' => [
                    'error' => $auth->getError()
                ]
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'failure',
                'data' => [
                    'error' => $e->getMessage()
                ]
            ], 401);
        }
    }

    public function register(Request $request)
    {
        $auth = $this->service->createAuth();
        $credentials = $request->only('email', 'password');

        try {
            $auth->createUserWithEmailAndPassword($credentials['email'], $credentials['password']);
            $data = $auth->data();
            return response()->json([
                'type' => 'success',
                'data' => [
                    'user' => $data,
                    'message' => 'Please check your email to confirm your account.',
                ]
            ], 200);
        } catch (RequestException $e) {
            return response()->json([
                'type' => 'failure',
                'data' => [
                    'error' => $auth->getError()
                ]
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'failure',
                'data' => [
                    'error' => $e->getMessage()
                ]
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        $auth = $this->service->createAuth();
        $bearerToken = $request->bearerToken();

        try {
            $response = $auth->logout($bearerToken);
            return response()->json([
                'type' => 'success',
                'message' => $response,
            ], 200);
        } catch (RequestException $e) {
            return response()->json([
                'type' => 'failure',
                'data' => [
                    'error' => $auth->getError()
                ]
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'failure',
                'data' => [
                    'error' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function getGoogleAuthUrlJson()
    {
        $url = $this->getGoogleAuthUrl();
        return response()->json([
            'type' => 'redirect',
            'data' => [
                'url' => $url
            ]
        ], 200);
    }

    public function getDiscordAuthUrlJson()
    {
        $url = $this->getDiscordAuthUrl();
        return response()->json([
            'type' => 'redirect',
            'data' => [
                'url' => $url
            ]
        ], 200);
    }

    public function getUserSession(Request $request)
    {
        $auth = $this->service->createAuth();
        $bearerToken = $request->bearerToken();

        try {
            $data = $auth->getUser($bearerToken);
            if ($data->aud !== 'authenticated')
                throw new \Exception('Invalid token');

            return response()->json([
                'type' => 'success',
                'data' => [
                    'token' => $bearerToken,
                    'user' => $data,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'failure',
                'data' => [
                    'error' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function recover(Request $request)
    {
        $auth = $this->service->createAuth();

        try {
            $auth->recoverPassword($request->email);
            return response()->json(['message' => 'Recovery email sent'], 200);
        } catch (RequestException $e) {
            return response()->json([
                'type' => 'failure',
                'data' => [
                    'error' => $auth->getError()
                ]
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'failure',
                'data' => [
                    'error' => $e->getMessage()
                ]
            ], 500);
        }
    }
}
