@extends('master')
@section('content')
<form method="POST" action="index.php">
    <h2>Estado del saldo actual</h2>
    <p>Tu saldo actual es: {{$saldo}}€</p>
    <button type="submit" id="volver" name="enviar" value="Volver a Personal">Volver</button>
</form>
@endsection


