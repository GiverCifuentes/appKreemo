<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;

use App\Models\Visita;
use App\Models\Obra;
use App\Models\ListaChequeo;

class VisitaController extends Controller
{

    public function index(){
        $visita = Visita::all();
        $obra = Obra::all();
        return view('visita.index', compact('obra'));
    }


    public function listarvisitas(Request $request){

        $obra = Obra::all();


        if ($request->ajax()) {

        $visita = Visita::select("visita.*", "obra.nombre as nombre_obra")
        ->join("obra", "visita.idobra", "=", "obra.id")
        ->get();

        return DataTables::of($visita)

        ->addColumn('listaChequeo', function ($visita) {
            if($visita->tipovisita == 'Técnica')
            {
                return '<a type="button" class="btn btn-primary" href="/listachequeo/crear/'.$visita->id.'" ><i class="fas fa-check"></i></a>';

            }
            else
            {
                return '<a type="button" class="btn btn-primary disabled"  href="/listachequeo/crear/'.$visita->id.'" ><i class="fas fa-check"></i></a>';

            }
        })

        ->rawColumns(['listaChequeo'])
        ->make(true);

        }
        return view('visita.listarvisita', compact('obra'));
    }

    public function create()
    {

    }

    public function pasarid($id)
    {
        $id;
        return view('servicio.create', compact('id'));
    }

    public function store(Request $request)
    {
        $data = request()->except(['_token','_method']);
        Visita::insert($data);
        print_r($data);
    }

    public function show()
    {

        $data['visita']= $visita = Visita::select("visita.*", "obra.nombre as nombre_obra")
           ->join("obra", "visita.idobra", "=", "obra.id")
            ->get();

            $nuevavisita=[];

            foreach ($visita as $value) {
                        $nuevavisita[]=[
                        "id"=>$value->id,
                        "start"=>$value->fecha." ".$value->horainicio,
                        "end"=>$value->fecha." ".$value->horafinal,
                        "obra"=>$value->idobra,
                        "tipovisita"=>$value->tipovisita,
                        "descripcion"=>$value->descripcion,
                        "title"=>$value->nombre_obra,
                        "textColor"=>"#fff"
                    ];
                    }
                    return response()->json($nuevavisita);

        return response()->json($nuevavisita);

    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $datosVisita = request()->except(['_token','_method']);
        $respuesta = Visita::where('id', '=', $id)->update($datosVisita);
        return response()->json($respuesta);
    }

    public function destroy($id)
    {
        $visita = Visita::findOrFail($id);
        Visita::destroy($id);
        return response()->json($id);
    }
}
