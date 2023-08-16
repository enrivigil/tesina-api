<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Sucursal;

class SucursalController extends Controller
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

            $sucursales = DB::table('sucursales as s')
                ->join('empresas as e', 's.empresa_id', '=', 'e.id')
                ->join('municipios as m', 's.municipio_id', '=', 'm.id')
                ->join('departamentos as d', 'm.departamento_id', '=', 'd.id')
                ->where('e.cliente_id', $cid)
                ->select('s.*', 'e.razon_social', 'd.id as depid', 'm.nombre as muni', 'd.nombre as depa')
                ->get();

            return response([
                'cod' => 200,
                'datos' => $sucursales,
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
                'telefono' => 'required',
                'direccion' => 'required',
                'municipio_id' => 'required',
                'empresa_id' => 'required',
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
                'empresa_id' => $req->input('empresa_id'),
            ];

            $sucursal = new Sucursal();
            $sucursal->nombre = $data['nombre'];
            $sucursal->telefono = $data['telefono'];
            $sucursal->direccion = $data['direccion'];
            $sucursal->municipio_id = $data['municipio_id'];
            $sucursal->empresa_id = $data['empresa_id'];
            $sucursal->activo = true;

            if (!$sucursal->save())
                return response([
                    'cod' => 500,
                    'msg' => 'Ups! Algo ha fallado',
                ], 500);

            return response([
                'cod' => 201,
                'msg' => 'Registrado exitosamente',
                'sucursal' => $sucursal,
            ], 201);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function detalles($id)
    {
        try {

            $sucursal = DB::table('sucursales as s')
                ->join('empresas as e', 's.empresa_id', '=', 'e.id')
                ->join('municipios as m', 's.municipio_id', '=', 'm.id')
                ->join('departamentos as d', 'm.departamento_id', '=', 'd.id')
                ->where('s.id', $id)
                ->select('s.*', 'e.razon_social', 'd.id as depid', 'm.nombre as muni', 'd.nombre as depa')
                ->first();

            if (!isset($sucursal) || is_null($sucursal))
                return response([
                    'cod' => 404,
                    'msg' => 'Recurso no econtrado',
                ], 404);

            return response([
                'cod' => 200,
                'sucursal' => $sucursal,
            ], 200);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function editar(Request $req, $id)
    {
        try {

            $sucursal = Sucursal::find($id);

            if (!isset($sucursal) || is_null($sucursal))
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
                'id' => $req->input('nombre'),
                'nombre' => $req->input('nombre'),
                'telefono' => $req->input('telefono'),
                'direccion' => $req->input('direccion'),
                'municipio_id' => $req->input('municipio_id'),
                'activo' => $req->input('activo'),
            ];

            $sucursal->nombre = $data['nombre'];
            $sucursal->telefono = $data['telefono'];
            $sucursal->direccion = $data['direccion'];
            $sucursal->municipio_id = $data['municipio_id'];
            $sucursal->activo = $data['activo'];

            if (!$sucursal->save())
                return response([
                    'cod' => 500,
                    'msg' => 'Ups! Algo ha fallado',
                ], 500);

            return response([
                'cod' => 201,
                'msg' => 'Editado exitosamente',
                'sucursal' => $sucursal,
            ], 201);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function eliminar($id)
    {
        try {

            $sucursal = Sucursal::find($id);

            if (!isset($sucursal) || is_null($sucursal))
                return response([
                    'cod' => 404,
                    'msg' => 'Recurso no econtrado',
                ], 404);

            if (!$sucursal->delete())
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
