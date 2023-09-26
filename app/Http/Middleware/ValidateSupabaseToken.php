<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Traits\SupabaseTrait;
use \GuzzleHttp\Exception\RequestException;

class ValidateSupabaseToken
{
    use SupabaseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $service = $this->initializeSupabaseService();
        $auth = $service->createAuth();
        $bearerToken = $request->bearerToken();

        try {
            $data = $auth->getUser($bearerToken);
            if ($data->aud !== 'authenticated')
                throw new \Exception('Invalid token');

            $request->attributes->set('auth', $auth);
            $request->attributes->set('user', $data);
        } catch (RequestException $e) {
            // $request->request->remove('auth');
            // $request->request->remove('user');
            return response()->json([
                'type' => 'failure',
                'data' => [
                    'error' => $auth->getError()
                ]
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'failure',
                'data' => [
                    'error' => $e->getMessage()
                ]
            ], 500);
        }

        return $next($request);
    }
}
