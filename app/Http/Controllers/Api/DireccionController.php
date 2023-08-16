<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Direccion;

class DireccionController extends Controller
{
    public function agregar(Request $req)
    {
        try {

            $validator = Validator::make($req->all(), [
                'nombre' => 'required',
                'telefono' => 'required',
                'direccion' => 'required',
                'municipio_id' => 'required',
                'destinatario_id' => 'required',
            ]);

            if ($validator->fails())
                return response([
                    'cod' => 400,
                    'msg' => 'Ups! Faltan algunos campos que llenar',
                    'errores' => $validator->errors(),
                ], 400);

            $data = [
                'nombre' => $req->input('nombre'),
                'telefono' => $req->input('telefono'),
                'direccion' => $req->input('direccion'),
                'municipio_id' => $req->input('municipio_id'),
                'destinatario_id' => $req->input('destinatario_id'),
            ];

            $direccion = new Direccion();
            $direccion->nombre = $data['nombre'];
            $direccion->telefono = $data['telefono'];
            $direccion->direccion = $data['direccion'];
            $direccion->municipio_id = $data['municipio_id'];
            $direccion->destinatario_id = $data['destinatario_id'];

            if (!$direccion->save())
                return response([
                    'cod' => 500,
                    'msg' => 'Ups! Algo ha fallado',
                ], 500);

            return response([
                'code' => 201,
                'msg' => 'Registrado exitosamente',
                'direccion' => $direccion,
            ], 201);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function editar(Request $req, $id)
    {
        try {

            $direccion = Direccion::find($id);

            if (!isset($direccion) || is_null($direccion))
                return response([
                    'cod' => 404,
                    'msg' => 'Recurso no econtrado',
                ], 404);

            $validator = Validator::make($req->all(), [
                'id' => 'required',
                'nombre' => 'required',
                'telefono' => 'required',
                'direccion' => 'required',
                'municipio_id' => 'required',
            ]);

            if ($validator->fails())
                return response([
                    'cod' => 400,
                    'msg' => 'Ups! Faltan algunos campos que llenar',
                    'errores' => $validator->errors(),
                ], 400);

            $data = [
                'id' => $req->input('id'),
                'nombre' => $req->input('nombre'),
                'telefono' => $req->input('telefono'),
                'direccion' => $req->input('direccion'),
                'municipio_id' => $req->input('municipio_id'),
            ];

            $direccion->nombre = $data['nombre'];
            $direccion->telefono = $data['telefono'];
            $direccion->direccion = $data['direccion'];
            $direccion->municipio_id = $data['municipio_id'];

            if (!$direccion->save())
                return response([
                    'cod' => 500,
                    'msg' => 'Ups! Algo ha fallado',
                ], 500);

            return response([
                'code' => 201,
                'msg' => 'Editado exitosamente',
                'direccion' => $direccion,
            ], 201);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function eliminar($id)
    {
        try {

            $direccion = Direccion::find($id);

            if (!isset($direccion) || is_null($direccion))
                return response([
                    'cod' => 404,
                    'msg' => 'Recurso no econtrado',
                ], 404);

            if (!$direccion->delete())
                return response([
                    'cod' => 500,
                    'msg' => 'Ups! Algo ha fallado',
                ], 500);

            return response([
                'cod' => 201,
                'msg' => 'Eliminado exitosamente',
            ], 200);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
