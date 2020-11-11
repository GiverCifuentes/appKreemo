<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Response;

use App\Models\Servicio;
use App\Models\EstadoServicio;
use App\Models\Cotizacion;
use App\Models\Maquinaria;
use App\Models\Operario;
use App\Models\Obra;
use App\Models\Ocupacion;
use App\Ocupacion as AppOcupacion;
use Facade\FlareClient\Http\Response as HttpResponse;

class ServicioController extends Controller
{
    public function index(){
        $servicio = Servicio::all();
        $estadoservicio = EstadoServicio::all();
        $cotizacion = Cotizacion::select("cotizacion.*", "empresa.nombre as nombre_empresa", "estadocotizacion.estado_cotizacion","modalidad.modalidad", "etapa.etapa", "jornada.jornada_nombre", "tipoconcreto.tipo_concreto","obra.nombre as nombre_obra", "obra.telefono1", "obra.correo1")
            ->join("empresa","cotizacion.idEmpresa", "=", "empresa.id")
            ->join("estadocotizacion", "cotizacion.idEstado", "=", "estadocotizacion.id")
            ->join("modalidad", "cotizacion.idModalidad", "=", "modalidad.id")
            ->join("etapa", "cotizacion.idEtapa", "=", "etapa.id")
            ->join("jornada", "cotizacion.idJornada", "=", "jornada.id")
            ->join("tipoconcreto", "cotizacion.idTipo_Concreto", "=", "tipoconcreto.id")
            ->join("obra", "cotizacion.idObra", "=", "obra.id")
            ->where("cotizacion.inicioBombeo",">", now())
            ->where("cotizacion.idestado", "=", 2)
            ->orwhere("cotizacion.idestado", "=", 4)
            ->orderBy("cotizacion.id")
            ->get();
        $maquinaria = Maquinaria::select("maquinaria.*")
        ->where("maquinaria.estado","=",1)
        ->get();
        $operario = Operario::all();
        return view('servicio.index', compact('estadoservicio','cotizacion','maquinaria','operario'));
    }

    public function listarservicios(Request $request){

        if ($request->ajax()) {
            $estadoservicio = EstadoServicio::all();

        $servicio = Servicio::select("servicio.*","maquinaria.modelo", "op1.nombre as n1","op2.nombre as n2","estadoservicio.estado")
        ->join("maquinaria", "servicio.idmaquina","=","maquinaria.id")
        ->join("operario as op1", "servicio.idoperario1","=","op1.id")
        ->join("operario as op2", "servicio.idoperario2","=","op2.id")
        ->join("estadoservicio", "servicio.idestadoservicio","=","estadoservicio.id")
        ->get();


        return DataTables::of($servicio)
        ->addColumn('editar', function ($servicio) {
            return '<a type="button" class="btn btn-primary"   href="/servicio/editar/'.$servicio->id.'" >Editar</a>';
        })
        ->addColumn('encuesta', function ($servicio) {
            return '<a type="button" class="btn btn-success" href="/encuesta/crear/'.$servicio->id.'" >Encuesta</a>';
        })
        ->rawColumns(['encuesta','editar'])
        ->make(true);

        }
        return view('/servicio/listarservicios');
    }

    public function validarFecha($fechainicio, $fechafin, $idmaquina)
    {
        $servicio = Servicio::select('servicio.*')
        ->where('idmaquina',"=",$idmaquina)
        ->whereDate('fechainicio','<=',$fechainicio)
        ->whereDate('fechainicio','>=',$fechainicio)
        ->first();

        return $servicio == null ? true :  false;
    }

    public function store(Request $request)
    {
        $data = request()->except(['_token','_method']);

        $resultado = $this->validarFecha($data["fechainicio"], $data["fechafin"],$data['idmaquina']);

        if($resultado == true)
        {
            Servicio::insert($data);

            print_r($data);

            $cotizacion = Cotizacion::find($data['idcotizacion']);
            $cotizacion->update(["idEstado"=>4]);

            $ocupacion = Ocupacion::insert([
                "idmaquina" => $data['idmaquina'],
                "fechainicio" => $data['fechainicio'],
                'fechafin' => $data['fechafin']
            ]);

            return response()->json(["ok"=>true]);
        }
        else
        {
            return response()->json(["ok"=>false]);
        }
    }

      public function edit($id){

        $servicio = servicio::find($id);

        $estadoservicio = EstadoServicio::all();
        $maquinaria = Maquinaria::all();
        $operario = Operario::all();
        $cotizacion = Cotizacion::select("cotizacion.*", "empresa.nombre as nombre_empresa", "estadocotizacion.estado_cotizacion","modalidad.modalidad", "etapa.etapa", "jornada.jornada_nombre", "tipoconcreto.tipo_concreto","obra.nombre as nombre_obra", "obra.telefono1", "obra.correo1")
            ->join("empresa","cotizacion.idEmpresa", "=", "empresa.id")
            ->join("estadocotizacion", "cotizacion.idEstado", "=", "estadocotizacion.id")
            ->join("modalidad", "cotizacion.idModalidad", "=", "modalidad.id")
            ->join("etapa", "cotizacion.idEtapa", "=", "etapa.id")
            ->join("jornada", "cotizacion.idJornada", "=", "jornada.id")
            ->join("tipoconcreto", "cotizacion.idTipo_Concreto", "=", "tipoconcreto.id")
            ->join("obra", "cotizacion.idObra", "=", "obra.id")
            ->where("cotizacion.inicioBombeo",">", now())
            ->where("cotizacion.idestado", "=", 2)
            ->orwhere("cotizacion.idestado", "=", 4)
            ->orderBy("cotizacion.id")
            ->get();

        if ($servicio==null) {

            return redirect("/servicio/listarservicios");
        }
        return view("servicio.edit", compact('id','servicio','estadoservicio','maquinaria','cotizacion','operario'));
    }

    public function create(){

        $servicio = Servicio::all();
        $estadoservicio = EstadoServicio::all();
        $maquinaria = Maquinaria::all();
        $cotizacion = Cotizacion::all();
        $operario = Operario::all();

         return view('/servicio/edit', compact ('servicio','estadoservicio','maquinaria','cotizacion','operario'));
     }

     function pasarfecha(Request $request){

        $input = $request->all();

        $cotizacion = Cotizacion::select('cotizacion.inicioBombeo')
        ->where("cotizacion.id","=", [$input["id"]])
        ->get();

        return response(json_encode($cotizacion), 200)->header('Content-type','text/plain');
     }

    public function show()
    {

        $cotizacion = Cotizacion::all();
        $obra = Obra::all();

        $data['servicio']= $servicio = Servicio::select("servicio.*", "cotizacion.idobra")
        ->join("cotizacion","cotizacion.id", "=", "servicio.idcotizacion")
         ->get();


            $nuevoservicio=[];

            foreach ($servicio as $value) {
                        $nuevoservicio[]=[
                        "id"=>$value->id,
                        "start"=>$value->fechainicio." ".$value->horainicio,
                        "end"=>$value->fechafin." ".$value->horafin,
                        "estadoservicio"=>$value->idestadoservicio,
                        "cotizacion"=>$value->idcotizacion,
                        "maquina"=>$value->idmaquina,
                        "operario1"=>$value->idoperario1,
                        "operario2"=>$value->idoperario2,
                        "descripcion"=>$value->descripcion,
                        "title"=>"Maq N° ".$value->idmaquina." - Obra N° ".$value->idobra,
                        "backgroundColor"=>$value->estado ==1 ? "#61B74B" : "#FAF31E",
                        "textColor"=>"#fff"
                    ];
                    }
                    return response()->json($nuevoservicio);

        return response()->json($nuevoservicio);

    }

    public function actualizar(Request $request)
    {
        $input = $request->all();
        try {

                $servicio = Servicio::find($input["id"]);

                if ($servicio==null) {
                    return redirect("/servicio/listarservicio");
                }

                $servicio->update([
                    "idestadoservicio" => $input["idestadoservicio"],
                    "idcotizacion" => $input["idcotizacion"],
                    "idmaquina" => $input["idmaquina"],
                    "idoperario1" => $input["idoperario1"],
                    "idoperario2" => $input["idoperario2"],
                    "fechainicio" => $input["fechainicio"],
                    "fechafin" => $input["fechafin"],
                    "horainicio" => $input["horainicio"],
                    "horafin" => $input["horafin"],

                    "descripcion" => $input["descripcion"],
                ]);

                return redirect("/servicio/listarservicio");

            } catch (\Exception $e ) {
                return redirect("/servicio/listarservicio");
            }
    }

    public function update(Request $request, $id)
    {
        $datosservicio = request()->except(['_token','_method']);
        $respuesta = Servicio::where('id', '=', $id)->update($datosservicio);
        return response()->json($respuesta);
    }

    public function updateState($id, $estado){
        $servicio = Servicio::find($id);

        if ($servicio==null) {
            return redirect("/servicio/listarservicios");
        }

        try {

            $servicio->update(["estado"=>$estado]);
            return redirect("/servicio/listarservicios");

        } catch (\Exception $e) {

            return redirect("/servicio/listarservicios");
        }
    }

    public function destroy($id)
    {
        $servicio = Servicio::findOrFail($id);
        Servicio::destroy($id);
        return response()->json($id);
    }
}

