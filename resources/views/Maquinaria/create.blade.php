@extends('layouts.app')

@section('body')
    <div class="card">
        <div class="card-header">
            <strong>Crear Maquina</strong>
            <a href="/maquinaria" class="btn btn-link">Volver</a>
            <!-- <a href="/tipocontacto/listar" class="btn btn-link">Listar Contacto</a> -->
        </div>
        <div class="card-body">
        @include('flash::message')
        <form action="/maquinaria/guardar" method="POST" enctype="multipart/form-data">
        @csrf
            <div class="row">

                <div class="col-6">
                    <div class="form-group">
                        <label for="">Serial Equipo</label>
                        <input value="{{old('serialequipo')}}" type="text" class="form-control @error('serialequipo') is-invalid @enderror"  name="serialequipo" id="serialequipo">
                        @error('serialequipo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group">
                        <label for="">Modelo</label>
                        <input value="{{old('modelo')}}" type="text" class="form-control @error('modelo') is-invalid @enderror"  name="modelo" id="modelo">
                        @error('modelo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group">
                        <label for="">Serial Motor</label>
                        <input value="{{old('serialmotor')}}" type="text" class="form-control @error('serialmotor') is-invalid @enderror"  name="serialmotor" id="serialmotor">
                        @error('serialmotor')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group">
                        <label for="">Observación</label>
                        <input value="{{old('observacion')}}" type="text" class="form-control @error('observacion') is-invalid @enderror"  name="observacion" id="observacion">
                        @error('observacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                </div>
            </div>
            <button type="submit" class="btn btn-success float-lg-right">Guardar</button>
            </form>
        </div>
    </div>
@endsection
