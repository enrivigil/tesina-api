<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;

use App\Models\Cliente;

class ClienteController extends Controller
{
    public function login(Request $req)
    {
        try {

            $validator = Validator::make($req->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails())
                return response([
                    'cod' => 400,
                    'msg' => 'Ups! Faltan algunos campos que llenar',
                    'errores' => $validator->errors(),
                ], 400);

            $data = [
                'email' => $req->input('email'),
                'password' => $req->input('password'),
            ];

            $cliente = DB::table('clientes')->where('email', $data['email'])->first();

            if (!isset($cliente))
                return response([
                    'cod' => 404,
                    'msg' => 'El usuario no existe',
                ], 404);

            if (!$cliente->activo)
                return response([
                    'code' => 401,
                    'msg' => 'El usuario está desactivado',
                ], 401);

            if (!Hash::check($data['password'], $cliente->password))
                return response([
                    'code' => 400,
                    'msg' => 'Contraseña incorrecta',
                ], 400);


            $key = env('JWT_KEY');
            $time = time();

            $payload = [
                'iat' => $time,
                'exp' => $time + 10000,
                'data' => [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre . ' ' . $cliente->apellido,
                    'email' => $cliente->email,
                ],
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');

            return response ([
                'code' => 200,
                'msg' => 'Autenticado exitosamente',
                'usuario' => [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre . ' ' . $cliente->apellido,
                    'email' => $cliente->email,
                    'jwt' => $jwt,
                ]
            ], 200);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function register(Request $req)
    {
        try {

            $validator = Validator::make($req->all(), [
                'nombre' => 'required',
                'apellido' => 'required',
                'dui' => 'required',
                'email' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails())
                return response([
                    'cod' => 400,
                    'msg' => 'Ups! Faltan algunos campos que llenar',
                    'errores' => $validator->errors(),
                ], 400);

            $data = [
                'nombre' => $req->input('nombre'),
                'apellido' => $req->input('apellido'),
                'dui' => $req->input('dui'),
                'email' => $req->input('email'),
                'password' => $req->input('password'),
            ];

            $cliente = new Cliente();

            $cliente->nombre = $data['nombre'];
            $cliente->apellido =$data['apellido'];
            $cliente->dui = $data['dui'];
            $cliente->email = $data['email'];
            $cliente->password = Hash::make($data['password']);
            $cliente->activo = false;

            if (!$cliente->save())
                return response([
                    'cod' => 500,
                    'msg' => 'Ups! Algo ha fallado',
                ], 500);

            return response([
                'code' => 201,
                'msg' => 'Registrado exitosamente',
                'cliente' => $cliente,
            ], 201);

        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
