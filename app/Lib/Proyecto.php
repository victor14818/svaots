<?php

namespace App\Lib;

class Proyecto
{
    public $id;
    public $name;
    public $description;
    public $proyectos;
    public $author;
    public $tiempoEstimado;

    function __construct($id,$nm,$dsc,$auth,$tiempo)
    {
        $this->id = $id;
	$this->name = $nm;
	$this->description = $dsc;
	$this->proyectos = array();
	$this->author = $auth;
	$this->tiempoEstimado = $tiempo;
    }
}

?>
