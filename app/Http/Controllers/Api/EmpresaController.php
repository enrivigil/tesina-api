<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Empresa;

class EmpresaController extends Controller
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

            $empresas = DB::table('empresas as e')
                ->join('clientes as c', 'e.cliente_id', '=', 'c.id')
                ->where('c.id', $cid)
                ->select(
                    'e.id',
                    'e.razon_social',
                    'e.nrc',
                    'e.activo',
                    'c.nombre',
                    'c.apellido'
                )
                ->get();

            return response([
                'cod' => 200,
                'datos' => $empresas,
            ], 200);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function agregar(Request $req)
    {
        try {

            $validator = Validator::make($req->all(), [
                'razon_social' => 'required',
                'nrc' => 'required',
                'cliente_id' => 'required',
            ]);

            if ($validator->fails())
                return response([
                    'cod' => 400,
                    'msg' => 'Ups! Faltan algunos campos que llenar',
                    'errores' => $validator->errors(),
                ], 400);

            $data = [
                'razon_social' => $req->input('razon_social'),
                'nrc' => $req->input('nrc'),
                'cliente_id' => $req->input('cliente_id'),
            ];

            $empresa = new Empresa();
            $empresa->razon_social = $data['razon_social'];
            $empresa->nrc = $data['nrc'];
            $empresa->cliente_id = $data['cliente_id'];
            $empresa->activo = true;

            if (!$empresa->save())
                return response([
                    'cod' => 500,
                    'msg' => 'Ups! Algo ha fallado',
                ], 500);

            return response([
                'code' => 201,
                'msg' => 'Registrado exitosamente',
                'empresa' => $empresa,
            ], 201);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function detalles($id)
    {
        try {

            $empresa = DB::table('empresas as e')
                ->join('clientes as c', 'e.cliente_id', '=', 'c.id')
                ->where('e.id', $id)
                ->select(
                    'e.id',
                    'e.razon_social',
                    'e.nrc',
                    'e.activo',
                    'c.nombre',
                    'c.apellido'
                )
                ->first();

            if (!isset($empresa) || is_null($empresa))
                return response([
                    'cod' => 404,
                    'msg' => 'Recurso no econtrado',
                ], 404);

            return response([
                'cod' => 200,
                'empresa' => $empresa,
            ], 200);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function editar(Request $req, $id)
    {
        try {

            $empresa = Empresa::find($id);

            if (!isset($empresa) || is_null($empresa))
                return response([
                    'cod' => 404,
                    'msg' => 'Recurso no econtrado',
                ], 404);

            $validator = Validator::make($req->all(), [
                'id' => 'required',
                'razon_social' => 'required',
                'nrc' => 'required',
                'cliente_id' => 'required',
                'activo' => 'required',
            ]);

            if ($validator->fails())
                return response([
                    'cod' => 400,
                    'msg' => 'Ups! Faltan algunos campos que llenar',
                    'errores' => $validator->errors(),
                ], 400);

            $data = [
                'id' => $req->input('id'),
                'razon_social' => $req->input('razon_social'),
                'nrc' => $req->input('nrc'),
                'cliente_id' => $req->input('cliente_id'),
                'activo' => $req->input('activo'),
            ];

            $empresa->razon_social = $data['razon_social'];
            $empresa->nrc = $data['nrc'];
            $empresa->activo = $data['activo'];

            if (!$empresa->save())
                return response([
                    'cod' => 500,
                    'msg' => 'Ups! Algo ha fallado',
                ], 500);

            return response([
                'cod' => 201,
                'msg' => 'Editado exitosamente',
                'empresa' => $empresa,
            ], 201);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function eliminar($id)
    {
        try {

            $empresa = Empresa::find($id);

            if (!isset($empresa) || is_null($empresa))
                return response([
                    'cod' => 404,
                    'msg' => 'Recurso no encontrado',
                ], 404);

            if (!$empresa->delete())
                return response([
                    'cod' => 500,
                    'msg' => 'Ups! Algo ha fallado',
                ], 500);

            return response([
                'cod' => 200,
                'msg' => 'Eliminado exitosamente',
            ], 200);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
