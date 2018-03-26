<?php

namespace App\Lib;

class Tarea
{
    public $id;
    public $projectId;
    public $projectName;
    public $status;
    public $assignedToId;
    public $assignedToName;
    public $subject;
    public $description;
    public $startDate;
    public $DueDate;
    public $RecepcionSolicitud;
    public $EnvioOT;
    public $BOEjecucion;
    public $IPEjecucion;

    function __construct($id,$projectId,$projectName,$status,$assignedToId,$assignedToName,$subject,$description,$startDate,$DueDate,$RecepcionSolicitud,$EnvioOT,$BOEjecucion,$IPEjecucion)
    {
        $this->id = $id;
    	$this->projectId = $projectId;
    	$this->projectName = $projectName;
    	$this->status = $status;
    	$this->assignedToId = $assignedToId;
    	$this->assignedToName = $assignedToName;
        $this->subject = $subject;
        $this->description = $description;
        $this->startDate = $startDate;
        $this->DueDate = $DueDate;
        $this->RecepcionSolicitud = $RecepcionSolicitud;
        $this->EnvioOT = $EnvioOT;
        $this->BOEjecucion = $BOEjecucion;
        $this->IPEjecucion = $IPEjecucion;
    }
}

?>
