<?php

require "vendor/autoload.php";
include 'functions.php';

try {
    $dsn = "mysql:host=localhost;dbname=contabilidad";
    $dbh = new PDO($dsn, "root", "");
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
}

use eftec\bladeone\bladeone;

$Views = __DIR__ . '\Views';
$Cache = __DIR__ . '\Cache';

$Blade = new BladeOne($Views, $Cache);

session_start();

// IMPORTANTE, IMPORTANTE!!!
/* Como la BDD proporcionada no estaba correctamente construida, es necesario añadir a la tabla apuntes
 * las columnas de concepto (varchar(20)) y fecha (date) y corregir la errata de
 * catidad y poner correctamente cantidad 
 */
if (empty($_POST)) {
    echo $Blade->run('index');
} else if (isset($_POST['enviar'])) {

    $boton = filter_input(INPUT_POST, 'enviar');
    switch ($boton) {
        case 'Iniciar Sesion':
            
            echo $Blade->run('iniciosesion');
            break;
        
        case 'Registrar':
            
            echo $Blade->run('registro');
            break;
        
        case 'Confirmar Inicio Sesion':
            
            $nombre = filter_input(INPUT_POST, 'nombre');
            $_SESSION['nombre'] = $nombre;
            $contra = filter_input(INPUT_POST, 'contra');

            $stmt = defaultSelect($dbh, 'SELECT * FROM usuarios');
            $encontrado = false;
            // Comprobamos los nombre y contraseña introducidos con los almacenados en la BDD
            while ($fila = $stmt->fetch()) {
                if ($nombre == $fila['nombre'] && $contra == $fila['password']) {
                    $encontrado = true;
                    break;
                }
            }

            if ($encontrado) {
                // Si el usuario y contraseña introducidos son correctos, seleccionamos el id asociado al usuario
                $stmt2 = paramRowSelect($dbh, $nombre, 'SELECT id FROM usuarios WHERE nombre=:param');
                $fila2 = $stmt2->fetch();
                $idusuario = $fila['id'];
                $_SESSION['idusuario'] = $idusuario;

                // Usamos el id que acabamos de seleccionar para poder coger los datos de apuntes asociados al usuario que inicia sesion
                $stmt3 = paramRowSelect($dbh, $idusuario, 'SELECT tipo, cantidad, concepto, fecha FROM apuntes WHERE id_user=:param');
                $fila3 = $stmt3->fetchAll();

                echo $Blade->run('personal', ['nombre' => $nombre, 'fila' => $fila3]);
            } else {
                $texto = "Credenciales incorrectas. Intentalo de nuevo";
                echo $Blade->run('iniciosesion', ['texto' => $texto]);
            }
            break;
            
        case 'Confirmar Registro':
            
            $nombre = filter_input(INPUT_POST, 'nombre');
            $contra = filter_input(INPUT_POST, 'contra');

            // Como el id no tiene auto-increment debemos seleccionar el maximo ID hasta el momento y sumarle 1 para evitar errores
            $stmt = defaultSelect($dbh, 'SELECT MAX(id)+1 FROM usuarios');
            $fila = $stmt->fetch();
            $idusuario = $fila['MAX(id)+1'];
            // Como en el primer registro aún no existirá ningun id lo ponemos a 0
            if (is_null($idusuario)) {
                $idusuario = 0;
            }

            // Insertamos los datos en la tabla usuarios
            $stmt2 = $dbh->prepare('INSERT INTO usuarios (id, nombre, password) VALUES (:id, :nombre, :password)');
            $stmt2->bindParam(':id', $idusuario);
            $stmt2->bindParam(':nombre', $nombre);
            $stmt2->bindParam(':password', $contra);
            $stmt2->execute();

            $texto = 'Registrado correctamente. Inicia sesion de nuevo';
            echo $Blade->run('iniciosesion', ['texto' => $texto]);
            break;
            
        case 'Añadir Apunte':
            
            echo $Blade->run('anadirapunte');
            break;
        
        case 'Confirmar Apunte':
            
            $tipo = filter_input(INPUT_POST, 'tipo');
            // Comprobamos que el tipo sea correcto para evitar errores de funcionalidad
            if ($tipo == 'ingreso' || $tipo == 'gasto') {
                $concepto = filter_input(INPUT_POST, 'concepto');
                $cantidad = filter_input(INPUT_POST, 'cantidad');
                $fecha = filter_input(INPUT_POST, 'fecha');
                $idusuario = $_SESSION['idusuario'];

                // Al igual que el id de usuario, el id de apuntes tampoco tiene auto-increment
                $stmt = defaultSelect($dbh, 'SELECT MAX(id)+1 FROM apuntes');
                $fila = $stmt->fetch();
                $idapunte = $fila['MAX(id)+1'];
                if (is_null($idapunte)) {
                    $idapunte = 0;
                }

                // Insertamos en la tabla apuntes los datos introducidos por el usuario
                $stmt2 = $dbh->prepare('INSERT INTO apuntes (id, id_user, tipo, cantidad, concepto, fecha) VALUES (:id, :id_user, :tipo, :cantidad, :concepto, :fecha)');
                $stmt2->bindParam(':id', $idapunte);
                $stmt2->bindParam(':id_user', $idusuario);
                $stmt2->bindParam(':tipo', $tipo);
                $stmt2->bindParam(':cantidad', $cantidad);
                $stmt2->bindParam(':concepto', $concepto);
                $stmt2->bindParam(':fecha', $fecha);
                $stmt2->execute();

                // Volvemos a obtener los apuntes asociados al usuario para mostrarlos actualizados en la pagina personal
                $stmt3 = paramRowSelect($dbh, $idusuario, 'SELECT tipo, cantidad, concepto, fecha FROM apuntes WHERE id_user=:param');
                $fila3 = $stmt3->fetchAll();

                echo $Blade->run('personal', ['nombre' => $_SESSION['nombre'], 'fila' => $fila3]);
            } else {
                $texto = 'El tipo debe ser ingreso o gasto. Vuelva a intentarlo';
                echo $Blade->run('anadirapunte', ['texto' => $texto]);
            }

            break;
            
        case 'Estado Saldo':
            
            $idusuario = $_SESSION['idusuario'];
            $tipo1 = 'ingreso';
            $tipo2 = 'gasto';

            // Seleccionamos la suma de los valores de la columna cantidad que sean de tipo ingreso y con el id del usuario actual
            $stmt = twoParamSelect($dbh, $tipo1, $idusuario, 'SELECT SUM(cantidad) FROM apuntes WHERE tipo=:param1 AND id_user=:param2');
            $fila = $stmt->fetch();
            $ingresos = $fila['SUM(cantidad)'];

            // Seleccionamos la suma de los valores de la columna cantidad que sean de tipo gasto y con el id del usuario actual
            $stmt2 = twoParamSelect($dbh, $tipo2, $idusuario, 'SELECT SUM(cantidad) FROM apuntes WHERE tipo=:param1 AND id_user=:param2');
            $fila2 = $stmt2->fetch();
            $gastos = $fila2['SUM(cantidad)'];
            $saldo = $ingresos - $gastos;

            echo $Blade->run('estadosaldo', ['saldo' => $saldo]);
            break;
        
        case 'Ingresos Periodo Tiempo':
            
            $fecha = filter_input(INPUT_POST, 'tiempo');
            $idusuario = $_SESSION['idusuario'];
            $tipo = 'ingreso';

            // Seleccionamos los apuntes que coincidan con una fecha mayor o igual a la introducida, el id del usuario actual y de tipo ingreso
            $stmt = $dbh->prepare('SELECT tipo, cantidad, concepto, fecha FROM apuntes WHERE fecha>=:fecha AND id_user=:id_user AND tipo=:tipo');
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':id_user', $idusuario);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            $fila = $stmt->fetchAll();
            
            // Si la selección está vacía se lo comunicaremos al usuario y si no, lo mostraremos por pantalla
            if (empty($fila)) {
                $texto = 'No tienes ingresos desde la fecha indicada';
                echo $Blade->run('ingresostiempo', ['fecha' => $fecha, 'texto' => $texto, 'fila' => $fila]);
            } else {
                echo $Blade->run('ingresostiempo', ['fecha' => $fecha, 'fila' => $fila]);
            }
            break;
            
        case 'Volver a Personal':
            
            $nombre = $_SESSION['nombre'];
            $idusuario = $_SESSION['idusuario'];

            // Cuando se pulse en un boton de Volver, obtenemos los apuntes asociados al usuario para llevarlos a la pagina personal
            $stmt = paramRowSelect($dbh, $idusuario, 'SELECT tipo, cantidad, concepto, fecha FROM apuntes WHERE id_user=:param');
            $fila = $stmt->fetchAll();

            echo $Blade->run('personal', ['nombre' => $nombre, 'fila' => $fila]);
            break;
        
        case 'Cerrar Sesion':
            
            echo $Blade->run('index');
            break;
        
    }
} else {
    echo $Blade->run('index');
}
