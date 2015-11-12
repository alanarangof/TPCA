<?php
/**
 * Created by PhpStorm.
 * User: mati
 * Date: 11/8/15
 * Time: 5:20 PM
 */

class dtoGrafo
{
    public $id;
    public $label;
    public $x;
    public $y;
    public $size;

    public function setID($id){
        $this->id = $id;
    }
    public function setLabel($label){
        $this->label = $label;
    }
    public function setX($x){
        $this->x = $x;
    }
    public function setY($y){
        $this->y = $y;
    }
    public function setSize($size){
        $this->size = $size;
    }
}

class dtoEdge
{
    public $id;
    public $source;
    public $target;

    public function setID($id){
        $this->id = $id;
    }
    public function setSource($source){
        $this->source = $source;
    }
    public function setTarget($target){
        $this->target = $target;
    }
}

class arrayGrafo
{
    public $arrayGrafo = array();

    public function saveObject()
    {
        array_push($this->arrayGrafo , $this->newarrayGrafo);
    }
    public function addGrafo(dtoGrafo $grafo2)
    {
        $this->newarrayGrafo = $grafo2;
        $this->saveObject();
    }
}

?>
