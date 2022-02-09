@extends('master')
@section('content')
<form method="POST" action="index.php">
    <h2>Añadir apunte</h2>
    <label for="tipo">Tipo:</label>
    <input type="text" id="tipo" name="tipo" /><br><br>
    <label for="concepto">Concepto:</label>
    <input type="text" id="concepto" name="concepto" /><br><br>
    <label for="cantidad">Cantidad:</label>
    <input type="number" step="0.01" id="cantidad" name="cantidad" /><br><br>
    <label for="fecha">Fecha:</label>
    <input type="date" id="fecha" name="fecha" /><br><br>
    <button type="submit" id="confirmarapunte" name="enviar" value="Confirmar Apunte">Confirmar</button><br><br>
    <button type="submit" id="volver" name="enviar" value="Volver a Personal">Volver</button>
    @if (!empty($texto))
    <p>{{$texto}}</p>
    @endif
</form>
@endsection

