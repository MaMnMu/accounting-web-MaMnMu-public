@extends('master')
@section('content')
<form method="POST" action="index.php">
    <h2>Registro</h2>
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" required /><br><br>
    <label for="contra">Contraseña:</label>
    <input type="password" id="contra" name="contra" required /><br><br>
    <button type="submit" id="continuarregistro" name="enviar" value="Confirmar Registro">Continuar</button>
</form>
@endsection
