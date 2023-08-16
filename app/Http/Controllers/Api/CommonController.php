<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommonController extends Controller
{
    public function listarDepartamentos()
    {
        try {

            $departamentos = DB::table('departamentos')->get();

            return response([
                'cod' => 200,
                'departamentos' => $departamentos,
            ], 200);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function listarMunicipios()
    {
        try {

            $municipios = DB::table('municipios')->get();

            return response([
                'cod' => 200,
                'municipios' => $municipios,
            ], 200);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function listarRutas()
    {
        try {

            $rutas = DB::table('rutas')->get();

            return response([
                'cod' => 200,
                'rutas' => $rutas,
            ], 200);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function listarDepartamentosPorRuta($id)
    {
        try {

            $departamentos = DB::table('rutas_detalles as rd')
                ->join('rutas as r', 'rd.ruta_id', '=', 'r.id')
                ->join('municipios as m', 'rd.municipio_id', '=', 'm.id')
                ->join('departamentos as d', 'm.departamento_id', '=', 'd.id')
                ->where('r.id', $id)
                ->select('d.id', 'd.nombre')
                ->groupBy('d.id')
                ->get();

            return response([
                'cod' => 200,
                'departamentos' => $departamentos,
            ], 200);

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function listarMunicipiosPorRutaYDepartamento($ruta_id, $dep_id)
    {
        try {

            $municipios = DB::table('rutas_detalles as rd')
                ->join('rutas as r', 'rd.ruta_id', '=', 'r.id')
                ->join('municipios as m', 'rd.municipio_id', '=', 'm.id')
                ->join('departamentos as d', 'm.departamento_id', '=', 'd.id')
                ->where([
                    'r.id' => $ruta_id,
                    'd.id' => $dep_id,
                ])
                ->select('m.id', 'm.nombre', 'rd.costo as costo', 'd.id as depid')
                ->get();

            return response([
                'cod' => 200,
                'municipios' => $municipios,
            ], 200);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
