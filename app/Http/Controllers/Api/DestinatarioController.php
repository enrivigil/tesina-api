<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Destinatario;
use App\Models\Direccion;

class DestinatarioController extends Controller
{
    public function index(Request $req)
    {
        try {

            $cid = $req->query('cid');

            if (is_null($cid))
                return response([
                    'cod' => 400,
                    'msg' => 'Ups! necesitas el paramatero del cliente id',
                ], 400);

            $destinatarios = Destinatario::with('direcciones.municipio.departamento')->where('cliente_id', $cid)->get();

            return response([
                'cod' => 200,
                'datos' => $destinatarios,
            ], 200);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function agregar(Request $req)
    {
        try {

            $validator = Validator::make($req->all(), [
                'nombre' => 'required',
                'apellido' => 'required',
                'dui' => 'required',
                'telefono' => 'required',
                'email' => 'required',
                'cliente_id' => 'required',
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
                'telefono' => $req->input('telefono'),
                'email' => $req->input('email'),
                'cliente_id' => $req->input('cliente_id'),
                'direcciones' => $req->input('direcciones') ?? [],
            ];

            $destinatario = new Destinatario();
            $destinatario->nombre = $data['nombre'];
            $destinatario->apellido = $data['apellido'];
            $destinatario->dui = $data['dui'];
            $destinatario->telefono = $data['telefono'];
            $destinatario->email = $data['email'];
            $destinatario->cliente_id = $data['cliente_id'];

            if (!$destinatario->save())
                return response([
                    'cod' => 500,
                    'msg' => 'Ups! Algo ha fallado',
                ], 500);

            foreach ($data['direcciones'] as $i) {

                $direccion = new Direccion();
                $direccion->nombre = $i['nombre'];
                $direccion->telefono = $i['telefono'];
                $direccion->direccion = $i['direccion'];
                $direccion->municipio_id = $i['municipio_id'];
                $direccion->destinatario_id = $destinatario->id;

                $direccion->save();
            }

            return response([
                'code' => 201,
                'msg' => 'Registrado exitosamente',
                'destinatario' => $destinatario,
            ], 201);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function detalles($id)
    {
        try {

            $destinatario = Destinatario::with('direcciones.municipio.departamento')->where('id', $id)->first();

            if (!isset($destinatario) || is_null($destinatario))
                return response([
                    'cod' => 404,
                    'msg' => 'Recurso no econtrado',
                ], 404);

            return response([
                'cod' => 200,
                'destinatario' => $destinatario,
            ], 200);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function editar(Request $req, $id)
    {
        try {

            $destinatario = Destinatario::find($id);

            if (!isset($destinatario) || is_null($destinatario))
                return response([
                    'cod' => 404,
                    'msg' => 'Recurso no econtrado',
                ], 404);

            $validator = Validator::make($req->all(), [
                'id' => 'required',
                'nombre' => 'required',
                'apellido' => 'required',
                'dui' => 'required',
                'telefono' => 'required',
                'email' => 'required',
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
                'apellido' => $req->input('apellido'),
                'dui' => $req->input('dui'),
                'telefono' => $req->input('telefono'),
                'email' => $req->input('email'),
            ];

            $destinatario->nombre = $data['nombre'];
            $destinatario->apellido = $data['apellido'];
            $destinatario->dui = $data['dui'];
            $destinatario->telefono = $data['telefono'];
            $destinatario->email = $data['email'];

            if (!$destinatario->save())
                return response([
                    'cod' => 500,
                    'msg' => 'Ups! Algo ha fallado',
                ], 500);

            return response([
                'cod' => 201,
                'msg' => 'Editado exitosamente',
                'destinatario' => $destinatario,
            ], 201);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function eliminar($id)
    {
        try {

            $destinatario = Destinatario::find($id);

            if (!isset($destinatario) || is_null($destinatario))
                return response([
                    'cod' => 404,
                    'msg' => 'Recurso no econtrado',
                ], 404);

            if (!$destinatario->delete())
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
