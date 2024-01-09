<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\PasswordResetNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**override el metodo default para recuperacion de contraseña
     * este metodo fue realizado para darle funcionalidad a la api
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }


    public function puestos()
    {
        return $this->belongsToMany('App\Puestos', 'usuarios_puestos', 'usuarios_id', 'puestos_id');
    }

    public function rol()
    {
        return $this->belongsTo('App\Roles', 'roles_id', 'id');
    }

    public function huellas()
    {
        return $this->hasMany('App\UsuariosHuellas', 'usuarios_id', 'id_user')->select("*", DB::raw(
            '(null) as huella_nombre'
        ), DB::raw(
            '(null) as huella_nombre_texto'
        ));
    }

    public function registros()
    {
        return $this->hasMany('App\RegistrosChecador', 'usuarios_id', 'id')
            ->orderBy('fecha_hora', 'asc');
    }
    public function horarios()
    {
        return $this->hasMany('App\HorariosChecador', 'usuarios_id', 'id');
    }

    public function dias_descanso()
    {
        return $this->hasMany('App\DiasDescanso', 'usuarios_id', 'id');
    }
}
