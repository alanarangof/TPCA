<?php
/**
 * Created by PhpStorm.
 * User: AlanNote
 * Date: 08/11/2015
 * Time: 17:56
 */
function getGrafoFromFile ($file){
    $archivo = fopen($file, "r", 1) or die("No se puede abrir el archivo!");
    $array = array();

    while(!feof($archivo)) {
        array_push($array, fgetcsv($archivo));
    }
    fclose($archivo);
    return $array;
}

function getTamañoGrafo($grafo){
    $tamaño=0;
    foreach ($grafo as $linea)
        $tamaño++;
    return $tamaño;

}
function inicializarGrafo($tamaño){
    $lineaInicializada = array();
    $grafo = array();
    for ($i=0;$i<$tamaño;$i++){
        array_push($lineaInicializada, 0);
    }
    for ($i=0;$i<$tamaño;$i++){
        array_push($grafo, $lineaInicializada);
    }
    return $grafo;
}

function mostrarGrafo($grafo){
    foreach ($grafo as $nodo) {
        foreach ($nodo as $dato)
            echo $dato;
        echo '<br>';
    }
    echo '<br>';
}

function getGrado($nodo){
    $grado = 0;
    foreach ($nodo as $eje){
        if ($eje == 1)
            $grado++;
    }
    return $grado;
}

function tieneCiclos($grafo){
    $ejeRemovido = true;
    while ($ejeRemovido){
        $ejeRemovido = false;
        for ($i=0;$i<getTamañoGrafo($grafo);$i++){
            if (getGrado($grafo[$i]) == 1){
                for ($j=0;$j<getTamañoGrafo($grafo);$j++){
                    if ($grafo[$i][$j] == 1) {
                        $grafo[$i][$j] = 0;
                        $grafo[$j][$i] = 0;
                    }
                }
                $ejeRemovido = true;
            }
        }
    }

    foreach ($grafo as $nodo)
        if (getGrado($nodo) > 0)
            return true;

    return false;

}

function getArbolMax ($grafoIn){
    $tamaño = getTamañoGrafo($grafoIn);
    $grafoOut = inicializarGrafo($tamaño);
    for ($i=0;$i<$tamaño;$i++){
        for ($j=0;$j<$i;$j++){
            if ($grafoIn[$i][$j] == 1){
                $grafoAux = $grafoOut;
                $grafoAux[$i][$j] = 1;
                $grafoAux[$j][$i] = 1;
                if (!tieneCiclos($grafoAux)){
                    $grafoOut = $grafoAux;
                }

            }
        }
    }
    return $grafoOut;
}

//EJECUCIÓN:

$grafoEntrada = getGrafoFromFile("grafo2.csv");

echo "Grafo de entrada:".'<br>';
mostrarGrafo($grafoEntrada);

$grafoSalida = getArbolMax($grafoEntrada);

echo "Grafo de salida:".'<br>';
mostrarGrafo($grafoSalida);




?>