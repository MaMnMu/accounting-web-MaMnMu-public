@extends('master')
@section('content')
<form method="POST" action="index.php">
    <button type="submit" id="iniciarsesion" name="enviar" value="Iniciar Sesion">Iniciar Sesion</button><br><br>
    <button type="submit" id="registrar" name="enviar" value="Registrar">Registrarse</button>
</form>
@endsection
