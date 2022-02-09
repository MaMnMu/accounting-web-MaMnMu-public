@extends('master')
@section('content')
<form method="POST" action="index.php">
    <h2>Tus ingresos desde la fecha: {{$fecha}} son:</h2>
    @foreach ($fila as $apunte)
    <p>Tipo: {{$apunte['tipo']}}, Concepto: {{$apunte['concepto']}}, Cantidad: {{$apunte['cantidad']}}€, Fecha: {{$apunte['fecha']}}</p>
    @endforeach
    @if (!empty($texto))
    <p>{{$texto}}</p>
    @endif
    <button type="submit" id="volver" name="enviar" value="Volver a Personal">Volver</button>
</form>
@endsection
