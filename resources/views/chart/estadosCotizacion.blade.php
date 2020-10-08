@extends('layouts.app')

@section('body')
    <div class="container">

            <div class="card col-md-5">
                <div class="card-header ">
                    <strong>Estados de cotizacion</strong>
                </div>
                <div class="card-body">
                    @include('flash::message')
                    <form method="POST" action="/chart/estadosCotizacion" id="form1">
                        @csrf
                            <div class="input-group">
                                <select class="custom-select" id="id" aria-label="Example select with button addon" name= "id">
                                    <option value="0">Seleccione un estado</option>
                                    @foreach($estadocotizacion as $key =>$value)
                                    <option value="{{ $value->id }}">{{ $value->estado_cotizacion}}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit">Generar graficos</button>
                                </div>
                            </div>
                            {{-- <label for="">Ingrese Empresa</label>
                            <div class="input-group mb-12">
                                <input type="text" class="form-control" placeholder="Empresa N°" aria-label="Recipient's username" aria-describedby="button-addon2" name="id">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Generar Reporte</button>
                                </div>
                            </div> --}}
                    </form>
                </div>
            </div>


        <div class="form row">
            <div class="form group col-md-6">
                <canvas id="myChart" width="400" height="400"></canvas>
            </div>
            <div class="form group col-md-6">
                <canvas id="myChart1" width="400" height="400"></canvas>
            </div>
        </div>



        <br>
        <div class="card">
            <div class="card-header text-white float-right" style="background-color: #616A6B">
                <strong>Reporte</strong>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="" align="center">
                        <tr>
                            <th>Notizacion N°</th>
                            <th>Fecha</th>
                            <th>Valor</th>
                            <th>Empresa</th>
                            <th>Obra</th>
                        </tr>
                        <tbody id="tbody" class="" align="center">

                        </tbody>
                    </thead>
                </table>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>
    <script>
        var fechaCotizacion = [];
        var valorTotal = [];
        var id = [];
        var nombre_empresa = [];
        var nombre_obra = [];
        var estado_cotizacion =[];

        $(document).ready(function(){
            $("#form1").submit(function(event){
                event.preventDefault();

                let validado = 0;

                if($("#id").val()== 0){
                    alert("elija id");
                }
                else{
                    validado++;
                }

                console.log(validado);

                if(validado == 1)
                {
                $.ajax({
                url:'/chart/estadosCotizacion',
                method: 'POST',
                data: {
                    id:$('select[name="id"]').val(),
                    _token:$('input[name="_token"]').val()
                }
            }).done(function(res){
                var arreglo = JSON.parse(res);
                for(var x= 0; x<arreglo.length;x++){
                    var todo = '<tr><td>'+arreglo[x].id+'</td>';
                    todo+='<td>'+arreglo[x].fechaCotizacion+'</td>';
                    todo+='<td>'+arreglo[x].valorTotal+'</td>';
                    todo+='<td>'+arreglo[x].nombre_empresa+'</td>';
                    todo+='<td>'+arreglo[x].nombre_obra+'</td></tr>';
                    $('#tbody').append(todo);
                    fechaCotizacion.push(arreglo[x].fechaCotizacion);
                    valorTotal.push(arreglo[x].valorTotal);
                    id.push(arreglo[x].id);
                    nombre_empresa.push(arreglo[x].nombre_empresa);
                    nombre_obra.push(arreglo[x].nombre_obra);
                    estado_cotizacion.push(arreglo[x].estado_cotizacion)
                }
                generarGrafica(nombre_obra,valorTotal,estado_cotizacion,nombre_empresa);
                generarGrafica2(nombre_obra,valorTotal,estado_cotizacion,nombre_empresa);
            })
                }else{
                    alert("Debe elegir Id.");
                }
            })
        });



        function generarGrafica(nombre_obra, valortotal,estado_cotizacion,id,nombre_empresa){
            var ctx = document.getElementById('myChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels:nombre_obra,
                    datasets: [{
                        label: 'Valor cotizacion',
                        data: valortotal,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(55, 159, 64, 0.2)',
                            'rgba(53, 102, 255, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(55, 159, 64, 1)',
                            'rgba(53, 102, 255, 0.2)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        }

        function generarGrafica2(nombre_obra, valortotal,estado_cotizacion,id,nombre_empresa){
            var ctx = document.getElementById('myChart1').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: nombre_obra,
                    datasets: [{
                        label: '# of Votes',
                        data: valortotal,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(55, 159, 64, 0.2)',
                            'rgba(53, 102, 255, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(55, 159, 64, 1)',
                            'rgba(53, 102, 255, 0.2)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        }
    </script>
@endsection
