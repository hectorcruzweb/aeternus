<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seguimientos extends Model
{
    protected $table = 'seguimientos';
    // Disable timestamps
    public $timestamps = false;

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'clientes_id', 'id');
    }


    protected $appends = ['fechahora_programada_texto', 'status_texto', 'fechahora_programada_texto_abr']; // 👈 this tells Laravel to include it in JSON

    public function getFechahoraProgramadaTextoAttribute()
    {
        if (!$this->fechahora_programada) {
            return null;
        }
        // Example format: "04 October 2025, 2:30 PM"
        return fechahora($this->fechahora_programada);
    }





    public function getStatusTextoAttribute()
    {
        if (!$this->status) {
            return null;
        }
        // Example format: "04 October 2025, 2:30 PM"
        $status = '';
        if ($this->status == 0) {
            $status = "Cancelado";
        } else
        if ($this->status == 1) {
            //just for programados_b
            $status = "Por atender";
        } else
        if ($this->status == 2) {
            $status = "Atendido";
        }
        return $status;
    }

    public function getFechahoraProgramadaTextoAbrAttribute()
    {
        if (!$this->fechahora_programada) {
            return null;
        }
        // Example format: "04 October 2025, 2:30 PM"
        return fecha_abr($this->fechahora_programada);
    }
}
