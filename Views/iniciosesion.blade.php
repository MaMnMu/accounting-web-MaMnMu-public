@extends('master')
@section('content')
<form method="POST" action="index.php">
    <h2>Inicio de sesion</h2>
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" required /><br><br>
    <label for="contra">Contraseña:</label>
    <input type="password" id="contra" name="contra" required /><br><br>
    <button type="submit" id="continuarinicio" name="enviar" value="Confirmar Inicio Sesion">Continuar</button>
    @if (!empty($texto))
    <p>{{$texto}}</p>
    @endif
</form>
@endsection
