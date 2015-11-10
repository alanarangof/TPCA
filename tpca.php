<?php
/**
 * Created by PhpStorm.
 * User: AlanNote
 * Date: 08/11/2015
 * Time: 17:56
 */
require('main.php');

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
function getCantNodosGrafo($grafo){
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
            echo $dato.' ';
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
        for ($i=0;$i<getCantNodosGrafo($grafo);$i++){
            if (getGrado($grafo[$i]) == 1){
                for ($j=0;$j<getCantNodosGrafo($grafo);$j++){
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
    $cantNodos = getCantNodosGrafo($grafoIn);
    $grafoOut = inicializarGrafo($cantNodos);
    for ($i=0;$i<$cantNodos;$i++){
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

$grafoSalida = getBosqueMax($grafoEntrada);

echo "Grafo de salida:".'<br>';
mostrarGrafo($grafoSalida);
jsonifyIn($grafoEntrada);
jsonifyOut($grafoSalida);

function jsonifyIn($grafoSalida){

    $DTOgrafo = new dtoGrafo();
    $DTOEdge = new dtoEdge();
    $size = 3;
    $j=0;
    $len = getCantNodosGrafo($grafoSalida);
    $fh = fopen('datain.json', 'w')  or die ("Error al abrir fichero de salida");
    fwrite($fh, "{ \n\"nodes\": [\n");
    for ($i=0;$i<getCantNodosGrafo($grafoSalida);$i++) {

        $DTOgrafo->setID("n".$i);
        $DTOgrafo->setX(rand(0,20));
        $DTOgrafo->setY(rand(0,20));
        $DTOgrafo->setLabel("entrada");
        $DTOgrafo->setSize($size);

        fwrite($fh, json_encode($DTOgrafo,JSON_UNESCAPED_UNICODE));
        if ($j < $len - 1) {
            // not the last
            fwrite($fh, ",\n");
        }
        $j++;
    }
    fwrite($fh, "\n],\n");

    //ejes
    fwrite($fh, " \n\"edges\": [\n");
    $num=0;
    for ($j=0;$j<getCantNodosGrafo($grafoSalida);$j++){
        for ($k=0;$k<getCantNodosGrafo($grafoSalida);$k++){
            if($grafoSalida[$j][$k]==1) {
                $DTOEdge->setID("e".$num);
                $DTOEdge->setSource("n".$j);
                $DTOEdge->setTarget("n".$k);
                fwrite($fh, json_encode($DTOEdge,JSON_UNESCAPED_UNICODE));
                if ($k < $len) {
                    // not the last
                    fwrite($fh, ",\n");
                }
                $num++;
            }
        }


    }
    fwrite($fh, "\n]\n}");
    fclose($fh);



}

function jsonifyOut($grafoSalida){

    $DTOgrafo = new dtoGrafo();
    $DTOEdge = new dtoEdge();
    $size = 3;
    $j=0;
    $len = getCantNodosGrafo($grafoSalida);
    $fh = fopen('dataout.json', 'w')  or die ("Error al abrir fichero de salida");
    fwrite($fh, "{ \n\"nodes\": [\n");
    for ($i=0;$i<getCantNodosGrafo($grafoSalida);$i++) {

        $DTOgrafo->setID("n".$i);
        $DTOgrafo->setX(rand(0,20));
        $DTOgrafo->setY(rand(0,20));
        $DTOgrafo->setLabel("salida");
        $DTOgrafo->setSize($size);

        fwrite($fh, json_encode($DTOgrafo,JSON_UNESCAPED_UNICODE));
        if ($j < $len - 1) {
            // not the last
            fwrite($fh, ",\n");
        }
        $j++;
    }
    fwrite($fh, "\n],\n");

    //ejes
    fwrite($fh, " \n\"edges\": [\n");
    $num=0;
    for ($j=0;$j<getCantNodosGrafo($grafoSalida);$j++){
        for ($k=0;$k<getCantNodosGrafo($grafoSalida);$k++){
            if($grafoSalida[$j][$k]==1) {
                $DTOEdge->setID("e".$num);
                $DTOEdge->setSource("n".$j);
                $DTOEdge->setTarget("n".$k);
                fwrite($fh, json_encode($DTOEdge,JSON_UNESCAPED_UNICODE));
                if ($j < $len - 1) {
                    // not the last
                    fwrite($fh, ",\n");
                }
                $num++;
            }
        }


    }
    fwrite($fh, "\n]\n}");
    fclose($fh);



}


?>