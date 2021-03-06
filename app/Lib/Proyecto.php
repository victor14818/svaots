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
    public $informedUsersEmails;
    public $archivoFormularioGenerico;

    function __construct($id,$nm,$dsc,$auth,$tiempo,$informedUsersEmails,$archivoFormGen)
    {
        $this->id = $id;
    	$this->name = $nm;
    	$this->description = $dsc;
    	$this->proyectos = array();
    	$this->author = $auth;
    	$this->tiempoEstimado = $tiempo;
        $this->informedUsersEmails = $informedUsersEmails;
        $this->archivoFormularioGenerico = $archivoFormGen;
    }
}

?>
