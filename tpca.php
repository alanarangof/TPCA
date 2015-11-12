
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

function getCantEjesGrafo($grafo)
{
    $cantEjes = 0;
    for ($i = 0; $i < getCantNodosGrafo($grafo); $i++) {
        for ($j = $i + 1; $j < getCantNodosGrafo($grafo); $j++) {
            if ($grafo[$i][$j] == 1)
                $cantEjes++;
        }

    }
    return $cantEjes;
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
    $nodoRemovido = true;
    $cantNodos = getCantNodosGrafo($grafo);
    //Para detectar ciclos utilizo el metodo visto en clase que consiste en eliminar nodos de grado <= a 1
    while ($nodoRemovido){//siempre y cuando pueda remover un nodo continuo
        $nodoRemovido = false;
        for ($i=0;$i<$cantNodos;$i++){ //recorro nodo por nodo
            $gradoNodo = getGrado($grafo[$i]);
            if ($gradoNodo <= 1){ //verifico si el nodo tiene grado <= 1
                //si es asi, remuevo el nodo
                unset ($grafo[$i]); //remuevo fila del nodo de la matriz
                foreach ($grafo as &$nodo) //remuevo columna del nodo de la matriz
                    unset ($nodo[$i]);
                $nodoRemovido = true;
            }
        }
        $grafo = array_values($grafo); //reindexo el grafo
        $cantNodos = getCantNodosGrafo($grafo); //actualizo cantidad de nodos
    }

    //si queda algun nodo en el grafo, tiene ciclos
    if (count($grafo) > 0)
        return true;
    //si no quedan nodos no tiene ciclos
    else
        return false;

}

//dado un grafo en forma de arraz de dos dimensiones devuelve el bosque generador m�ximo del mismo
function getBosqueMax ($grafoIn){
    $cantNodos = getCantNodosGrafo($grafoIn);
    $grafoOut = inicializarGrafo($cantNodos);
    //aplico un algoritmo similar a Prim agregando cada eje y checkeando que no genere ciclos
    for ($i=0;$i<$cantNodos;$i++){ //recorro cada nodo
        for ($j=0;$j<$i;$j++){ //para optiizar el tiempo de ejecución reviso los ejes del nodo anteriores a la diagonal, ya que al no ser dirigido el grafo la matriz está espejada por la diagonal
            if ($grafoIn[$i][$j] == 1){
                $grafoAux = $grafoOut;
                $grafoAux[$i][$j] = 1;//agrego eje
                $grafoAux[$j][$i] = 1;//agrego el mismo eje en el nodo opuesto ya que no es dirigido el grafo
                if (!tieneCiclos($grafoAux)){ //si el eje no generó un ciclos, guardo el grafo
                    $grafoOut = $grafoAux;
                }

            }
        }
    }
    return $grafoOut;
}

function jsonifyIn($grafoEntrada, $grafoSalida){
    $xPos = Array(0,1,0,1,2,3,2,3,0,1,2,3);
    $yPos = Array(0,0.5,1,1.5,0,0.5,1,1.5,2,2.5,2,2.5);
    $DTOgrafo = new dtoGrafo();
    $DTOEdge = new dtoEdge();
    $size = 3;
    $j=0;
    $len = getCantNodosGrafo($grafoEntrada);
    $fh = fopen('datain.json', 'w')  or die ("Error al abrir fichero de salida");
    $fg = fopen('dataout.json', 'w')  or die ("Error al abrir fichero de salida");
    fwrite($fh, "{ \n\"nodes\": [\n");
    fwrite($fg, "{ \n\"nodes\": [\n");
    for ($i=0;$i<getCantNodosGrafo($grafoEntrada);$i++) {

        $DTOgrafo->setID("n".$i);
        $DTOgrafo->setX($xPos[$i]);
        $DTOgrafo->setY($yPos[$i]);
        $DTOgrafo->setLabel("n".$i);
        $DTOgrafo->setSize($size);

        fwrite($fh, json_encode($DTOgrafo,JSON_UNESCAPED_UNICODE));
        fwrite($fg, json_encode($DTOgrafo,JSON_UNESCAPED_UNICODE));
        if ($j < $len - 1) {
            // not the last
            fwrite($fh, ",\n");
            fwrite($fg, ",\n");
        }
        $j++;
    }
    fwrite($fh, "\n],\n");
    fwrite($fg, "\n],\n");
    //ejes
    fwrite($fh, " \n\"edges\": [\n");
    fwrite($fg, " \n\"edges\": [\n");
    $num1=0;
    for ($j1=0;$j1<getCantNodosGrafo($grafoEntrada);$j1++){
        for ($k1=$j1+1;$k1<getCantNodosGrafo($grafoEntrada);$k1++){
            if($grafoEntrada[$j1][$k1]==1) {
                $DTOEdge->setID("e".$num1);
                $DTOEdge->setSource("n".$j1);
                $DTOEdge->setTarget("n".$k1);
                fwrite($fh, json_encode($DTOEdge,JSON_UNESCAPED_UNICODE));
                $num1++;
                if ($num1<getCantEjesGrafo($grafoEntrada)) {
                    // not the last
                    fwrite($fh, ",\n");
                }

            }
        }
    }
    $num2=0;
    for ($j=0;$j<getCantNodosGrafo($grafoSalida);$j++){
        for ($k=$j+1;$k<getCantNodosGrafo($grafoSalida);$k++){
            if($grafoSalida[$j][$k]==1) {
                $DTOEdge->setID("e".$num2);
                $DTOEdge->setSource("n".$j);
                $DTOEdge->setTarget("n".$k);
                fwrite($fg, json_encode($DTOEdge,JSON_UNESCAPED_UNICODE));
                $num2++;
                if ($num2<getCantEjesGrafo($grafoSalida)) {
                    // not the last
                    fwrite($fg, ",\n");
                }

            }
        }
    }

    fwrite($fh, "\n]\n}");
    fwrite($fg, "\n]\n}");
    fclose($fh);
    fclose($fg);
}



//EJECUCIÓN:
if (isset($_GET['file'])){
    $grafoEntrada = getGrafoFromFile($_GET['file']);


    echo "Grafo de entrada:" . '<br>';
    mostrarGrafo($grafoEntrada);


    $grafoSalida = getBosqueMax($grafoEntrada);

    echo "Grafo de salida:" . '<br>';
    mostrarGrafo($grafoSalida);
    jsonifyIn($grafoEntrada, $grafoSalida);
}
?>
