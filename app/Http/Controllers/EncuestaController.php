<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Flash;
use DataTables;

use App\Models\Encuesta;
use App\Models\Obra;
use App\Models\Empresa;
use App\Models\Servicio;


class EncuestaController extends Controller
{
    public function index(){

        $servicio = Servicio::all();
        return view('encuesta.index', compact('servicio'));

    }

    public function listar(Request $request){


        $encuesta = Encuesta::all();

        return DataTables::of($encuesta)

        ->addColumn('eliminar', function ($encuesta) {
            return '<a class="btn btn-primary btn-sm" href="/encuesta/ver/'.$encuesta->id.'">Ver encuesta</a>';
        })
        ->rawColumns(['eliminar'])
        ->make(true);

    }

    public function show($id)
    {
        $encuesta = Encuesta::find($id);


    if ($encuesta==null) {

        Flash::error("Encuesta no encontrada");
        return redirect("/encuesta");
    }
    //else{
        return view("encuesta.show", compact("encuesta"));
    // }
    }

    public function pasarid($id)
    {

        $empresa = Empresa::select("empresa.*", "empresa.nombre", "empresa.correo1", "empresa.telefono1")
        ->join("cotizacion", "cotizacion.idEmpresa","=","empresa.id")
        ->join("servicio", "servicio.idcotizacion","=","cotizacion.id")
        ->where("servicio.id", $id)
        ->get();

        $id;
        return view('encuesta.create', compact('id', 'empresa'));
    }

    public function create(){

        $encuesta = Encuesta::all();
        $servicio = Servicio::all();
        $obra = Obra::all();

        return view('encuesta.create', compact("servicio"));
    }

    public function save(Request $request){

        $request->validate(Encuesta::$rules);

        $input = $request->all();

        try {

            Encuesta::create([
                "idservicio" => $input["idservicio"],
                "directorobra" => $input["directorobra"],
                "constructora" => $input["constructora"],
                "correo" => $input["correo"],
                "celular" => $input["celular"],
                "mes" => $input["mes"],
                "respuesta1_1" => $input["respuesta1_1"],
                "respuesta1_2" => $input["respuesta1_2"],
                "respuesta1_3" => $input["respuesta1_3"],
                "respuesta1_4" => $input["respuesta1_4"],
                "respuesta2" => $input["respuesta2"],
                "respuesta3" => $input["respuesta3"],
                "respuesta4" => $input["respuesta4"],
                "respuesta5" => $input["respuesta5"],
                "respuesta6" => $input["respuesta6"],
                "respuesta7" => $input["respuesta7"]
            ]);

            Flash::success("Encuesta registrada correctamente");
            return redirect("/encuesta");

        } catch (\Exception $e ) {
            Flash::error($e->getMessage());
            return redirect("/encuesta/crear");
        }
    }

    public function destroy($id)
    {
        $encuesta = Encuesta::find($id);

        if (empty($encuesta)) {
            Flash::error('Encuesta no encontrado');

            return redirect('/encuesta');
        }

        $encuesta->delete($id);

        Flash::success('Encuesta eliminado.');

        return redirect('/encuesta');
    }
}
