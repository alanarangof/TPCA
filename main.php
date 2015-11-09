<?php
/**
 * Created by PhpStorm.
 * User: mati
 * Date: 11/8/15
 * Time: 5:20 PM
 */


$grafo = array (array(0,1,0,1),array(1,0,1,0),array(0,1,0,1),array(1,0,1,0));

foreach ($grafo as $row){
    echo '<tr>';
    foreach ($row as $item){
        echo '<td>'.$item.'</td>';
    }
    echo '</tr>';
}
?>
