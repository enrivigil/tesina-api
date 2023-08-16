<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class HasToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {

            $authHeader = $request->header('Authorization');

            if (is_null($authHeader) || empty($authHeader))
                return response([
                    'cod' => 401,
                    'msg' => 'No proveiste un token',
                ], 401);

            $token = explode(' ', $authHeader)[1];

            $key = env('JWT_KEY');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            return $next($request);

        } catch (ExpiredException $ee) {
            return response([
                'code' => 500,
                'msg' => 'Token expirado',
            ], 500);
        } catch (SignatureInvalidException $sie) {
            return response([
                'code' => 500,
                'msg' => 'Token invalido',
            ], 500);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
