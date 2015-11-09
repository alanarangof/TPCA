<?php
/**
 * Created by PhpStorm.
 * User: AlanNote
 * Date: 08/11/2015
 * Time: 17:56
 */

//Convierte una matriz de adyacencia guardada en un csv en un array de dos dimensiones;
function getGrafoFromFile ($file){
    $archivo = fopen($file, "r", 1) or die("No se puede abrir el archivo!");
    $array = array();

    while(!feof($archivo)) {
        array_push($array, fgetcsv($archivo));
    }
    fclose($archivo);
    return $array;
}

//dado un grafo en forma de arraz de dos dimensiones devuelve la cantidad de nodos del mismo
function getTamanoGrafo($grafo){
    $cantNodos=0;
    foreach ($grafo as $linea)
        $cantNodos++;
    return $cantNodos;

}

//dada una cantidad de nodos, genera una matriz de adyacencia en un array de dos dimenciones con esa cantidad
//de nodos con todos los valores en 0
function inicializarGrafo($cantNodos){
    $lineaInicializada = array();
    $grafo = array();
    for ($i=0;$i<$cantNodos;$i++){
        array_push($lineaInicializada, 0);
    }
    for ($i=0;$i<$cantNodos;$i++){
        array_push($grafo, $lineaInicializada);
    }
    return $grafo;
}

//dado un grafo en forma de arraz de dos dimensiones muestra en pantalla la matriz de adyacencia
function mostrarGrafo($grafo){
    foreach ($grafo as $nodo) {
        foreach ($nodo as $dato)
            echo $dato;
        echo '<br>';
    }
    echo '<br>';
}

//dado un nodo en forma de array de una dimension devuelve el grado del mismo
function getGrado($nodo){
    $grado = 0;
    foreach ($nodo as $eje){
        if ($eje == 1)
            $grado++;
    }
    return $grado;
}

//dado un grafo en forma de arraz de dos dimensiones devuelve un booleano en base a si el grafo tiene ciclos
function tieneCiclos($grafo){
    $ejeRemovido = true;
    while ($ejeRemovido){
        $ejeRemovido = false;
        for ($i=0;$i<getTamanoGrafo($grafo);$i++){
            if (getGrado($grafo[$i]) == 1){
                for ($j=0;$j<getTamanoGrafo($grafo);$j++){
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

//dado un grafo en forma de arraz de dos dimensiones devuelve el bosque generador m�ximo del mismo
function getBosqueMax ($grafoIn){
    $tamano = getTamanoGrafo($grafoIn);
    $grafoOut = inicializarGrafo($tamano);
    for ($i=0;$i<$tamano;$i++){
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

//EJECUCI�N:

$grafoEntrada = getGrafoFromFile("grafo2.csv");

echo "Grafo de entrada:".'<br>';
mostrarGrafo($grafoEntrada);

$grafoSalida = getArbolMax($grafoEntrada);

echo "Grafo de salida:".'<br>';
mostrarGrafo($grafoSalida);




?>