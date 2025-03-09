<?php

namespace App\Http\Controllers;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Event\Code\Throwable;

class ApiGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function login(Request $request)
    {
        try {

            $response = Http::retry(3, 1000)
                ->timeout(60000)->post(env('AUTH_SERVICE_URL') . '/api/v1/auth/login', $request->all());
            return response()->json($response->json());
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function checkToken(Request $request)
    {
        $token = $request->header('Authorization');
        // Verificar si el token está presente
        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            // Eliminar el prefijo 'Bearer ' del token si está presente
            $token = str_replace('Bearer ', '', $token);


            // Decodifica el token JWT
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            // Devuelve el contenido del token si no ha expirado
            return response()->json(['data' => $decoded]);
        } catch (ExpiredException $e) {
            // Maneja el caso cuando el token ha expirado
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (\Exception $e) {
            // Maneja cualquier otro error
            return response()->json(['error' => 'Invalid token: ' . $e->getMessage()], 401);
        }
    }


    public function logout(Request $request)
    {
        try {
            $token = $request->header('Authorization');
            $token = str_replace("Bearer ", '', $token);
            $logoutResponse =  Http::withToken($token)
                ->timeout(60)->post(env('AUTH_SERVICE_URL') . '/api/v1/auth/logout');
            return response()->json($logoutResponse->json());
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function register(Request $request)
    {
        try {
            $registerResponse = Http::timeout(600)->post(env('AUTH_SERVICE_URL') . '/api/v1/auth/register', $request->all());
            return $registerResponse->json();
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }

    public function me(Request $request)
    {
        try {
            $token = $request->header('Authorization');
            $token = str_replace("Bearer ", '', $token);

            $registerMe = Http::withToken($token)
                ->timeout(600)->get(env('AUTH_SERVICE_URL') . '/api/v1/auth/me');
            return $registerMe->json();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function refresh(Request $request)
    {
        try {
            $refreshResponse = Http::timeout(600)->post(env('AUTH_SERVICE_URL') . '/api/v1/auth/refresh', $request->all());
            return response()->json($refreshResponse->json());
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
