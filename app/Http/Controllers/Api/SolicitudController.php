<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Solicitud;

class SolicitudController extends Controller
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

            $solicitudes = DB::table('solicitudes as s')
                ->join('sucursales as su', 's.sucursal_id', '=', 'su.id')
                ->join('empresas as e', 'su.empresa_id', '=', 'e.id')
                ->join('estados as est', 's.estado_id', '=', 'est.id')
                ->join('direcciones_destinatarios as dd', 's.direccion_destinatario_id', '=', 'dd.id')
                ->join('destinatarios as d', 'dd.destinatario_id', '=', 'd.id')
                ->join('municipios as m', 'dd.municipio_id', '=', 'm.id')
                ->join('departamentos as dep', 'm.departamento_id', '=', 'dep.id')
                ->where('e.cliente_id', $cid)
                ->select('s.*', 'd.nombre as dest_nombre', 'd.apellido as dest_apellido', 'dd.direccion', 'm.nombre as mun', 'dep.nombre as dep', 'est.nombre as estado',)
                ->get();

            echo json_encode($solicitudes);
            return;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function agregar(Request $req)
    {
        try {

            $validator = Validator::make($req->all(), [
                'descripcion' => 'required',
                'observacion' => 'required',
                'sucursal_id' => 'required',
                'direccion_destinatario_id' => 'required',
                'costo' => 'required',
            ]);

            if ($validator->fails())
                return response([
                    'cod' => 400,
                    'msg' => 'Ups! Faltan algunos campos que llenar',
                    'errores' => $validator->errors(),
                ], 400);

            $data = [
                'descripcion' => $req->input('descripcion'),
                'observacion' => $req->input('observacion'),
                'sucursal_id' => $req->input('sucursal_id'),
                'direccion_destinatario_id' => $req->input('direccion_destinatario_id'),
                'costo' => $req->input('costo'),
            ];

            $solicitud = new Solicitud();
            $num_cod = Solicitud::max('num_codigo');
            $cod = 0;

            if (is_null($num_cod)) $cod = 1001;
            else $cod = $num_cod + 1;

            $prefijo_codigo = 'ENV';
            $num_codigo = $cod;
            $sufijo_codigo = 'SV';

            $solicitud->prefijo_codigo = $prefijo_codigo;
            $solicitud->num_codigo = $num_codigo;
            $solicitud->sufijo_codigo = $sufijo_codigo;
            $solicitud->descripcion = $data['descripcion'];
            $solicitud->observacion = $data['observacion'];
            $solicitud->sucursal_id = $data['sucursal_id'];
            $solicitud->direccion_destinatario_id = $data['direccion_destinatario_id'];
            $solicitud->costo = $data['costo'];
            $solicitud->estado_id = 1;

            if (!$solicitud->save())
                return response([
                    'cod' => 500,
                    'msg' => 'Ups! Algo ha fallado',
                ], 500);

            date_default_timezone_set('America/El_Salvador');
            $date = date('Y-m-d H:i:s');

            DB::table('historial_movimientos')
                ->insert([
                    'fecha' => $date,
                    'descripcion' => 'El estado de la solicitud es: Notifcado',
                    'estado_id' => 1,
                    'solicitud_id' => $solicitud->id,
                ]);

            return response([
                'code' => 201,
                'msg' => 'Registrado exitosamente',
                'solicitud' => $solicitud,
            ], 201);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function detalles($id)
    {
        try {

            $solicitud = DB::table('solicitudes  as s')
                ->join('estados as e', 's.estado_id', 'e.id')
                ->join('sucursales as su', 's.sucursal_id', 'su.id')
                ->join('empresas as em', 'su.empresa_id', 'em.id')
                ->join('direcciones_destinatarios as dd', 's.direccion_destinatario_id', 'dd.id')
                ->join('municipios as m', 'dd.municipio_id', 'm.id')
                ->join('departamentos as dt', 'm.departamento_id', 'dt.id')
                ->join('destinatarios as d', 'dd.destinatario_id', 'd.id')
                ->select(
                    's.id as id',
                    'em.razon_social as empresa',
                    DB::raw('CONCAT(d.nombre," ",d.apellido) as cliente'),
                    'd.dui as dui',
                    'd.telefono as telefono',
                    DB::raw('CONCAT(dd.direccion,", ",m.nombre,", ",dt.nombre) as direccion'),
                    's.descripcion as paquete',
                    'e.nombre as estado'
                )
                ->where('s.id', $id)->first();

            $historial = DB::table('historial_movimientos as hm')
                ->join('estados as e', 'hm.estado_id', 'e.id')
                ->join('solicitudes as s', 'hm.solicitud_id', 's.id')
                ->select(
                    'hm.fecha as fecha',
                    'hm.descripcion',
                    'e.nombre as estado'
                )
                ->where('solicitud_id', $solicitud->id)->get();

            return response([
                'solicitud' => $solicitud,
                'historial' => $historial
            ], 200);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function eliminar($id)
    {
        try {

            $solicitud = Solicitud::find($id);

            if (!isset($solicitud) || is_null($solicitud))
                return response([
                    'cod' => 404,
                    'msg' => 'Recurso no encontrado',
                ], 404);

            if (!$solicitud->delete())
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
