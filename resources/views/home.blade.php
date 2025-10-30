@extends('adminlte::page')

@section('title', 'Inicio')

@section('content_header')
    <h1>Bienvenido a Agenda Online</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <p>Redirigiendo al calendario...</p>
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    // Redirigir automáticamente al calendario después de 2 segundos
    setTimeout(function() {
        window.location.href = "{{ route('agenda.calendar') }}";
    }, 2000);
</script>
@stop