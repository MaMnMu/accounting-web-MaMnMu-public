<?php

// Construyo estas funciones para no repetir el mismo codigo multiples veces en el index.php

// Select sin parametros
function defaultSelect($dbh, $consulta) {
    $stmt = $dbh->prepare($consulta);
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute();
    return $stmt;
}

// Select con un parametro
function paramRowSelect($dbh, $param, $consulta) {
    $stmt = $dbh->prepare($consulta);
    $stmt->bindParam(':param', $param);
    $stmt->execute();
    return $stmt;
}

// Select con 2 parametros
function twoParamSelect($dbh, $param1, $param2, $consulta) {
    $stmt = $dbh->prepare($consulta);
    $stmt->bindParam(':param1', $param1);
    $stmt->bindParam(':param2', $param2);
    $stmt->execute();
    return $stmt;
}

