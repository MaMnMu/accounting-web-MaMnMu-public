@extends('master')
@section('content')
<form method="POST" action="index.php">
    <h2>Pagina personal de {{$nombre}}</h2>
    @foreach ($fila as $apunte)
    <p>Tipo: {{$apunte['tipo']}}, Concepto: {{$apunte['concepto']}}, Cantidad: {{$apunte['cantidad']}}€, Fecha: {{$apunte['fecha']}}</p>
    @endforeach
    <button type="submit" id="añadirapunte" name="enviar" value="Añadir Apunte">Añadir apunte</button><br><br>
    <button type="submit" id="estadosaldo" name="enviar" value="Estado Saldo">Comprobar estado del saldo actual</button><br><br>
    <label for="tiempo">Indique la fecha a partir de la cual mostrar los apuntes:</label>
    <input type="date" id="tiempo" name="tiempo" />
    <button type="submit" id="ingresostiempo" name="enviar" value="Ingresos Periodo Tiempo">Comprobar ingresos en un periodo de tiempo</button><br><br>
    <button type="submit" id="cerrarsesion" name="enviar" value="Cerrar Sesion">Cerrar Sesion</button>
</form>
@endsection


