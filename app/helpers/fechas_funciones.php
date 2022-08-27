<?php

function fechahora_completa()
{
    $arrayMeses = array(
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    );

    $arrayDias = array(
        'Domingo', 'Lunes', 'Martes',
        'Miércoles', 'Jueves', 'Viernes', 'Sabado'
    );
    return ($arrayDias[date('w')] . ", " . date('d') . " de " . $arrayMeses[date('m') - 1] . " de " . date('Y') . ", " . date("h:i:s a"));
}

function fecha_completa()
{
    $arrayMeses = array(
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    );


    return (date('d') . "/" . $arrayMeses[date('m') - 1] . "/" . date('Y'));
}



function mes($mes)
{
    $arrayMeses = array(
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    );
    return $arrayMeses[$mes - 1];
}



function fechahora($fecha)
{
    $arrayMeses = array(
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    );

    $arrayDias = array(
        'Domingo', 'Lunes', 'Martes',
        'Miércoles', 'Jueves', 'Viernes', 'Sabado'
    );
    return strtoupper(($arrayDias[date('w', strtotime($fecha))] . ", " . date('d', strtotime($fecha)) . " de " . $arrayMeses[date('m', strtotime($fecha)) - 1] . " de " . date('Y', strtotime($fecha)) . ", " . date("h:i:s a", strtotime($fecha))));
}


function hora($fecha)
{
    return date("h:i a", strtotime($fecha));
}




function fecha_only($fecha)
{
    $arrayMeses = array(
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    );
    $arrayDias = array(
        'Domingo', 'Lunes', 'Martes',
        'Miércoles', 'Jueves', 'Viernes', 'Sabado'
    );

    return strtoupper(($arrayDias[date('w', strtotime($fecha))] . ", " . date('d', strtotime($fecha)) . " de " . $arrayMeses[date('m', strtotime($fecha)) - 1] . " de " . date('Y', strtotime($fecha))));
}


function fecha_no_day($fecha)
{
    $arrayMeses = array(
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    );
    return strtoupper(date('d', strtotime($fecha)) . " de " . $arrayMeses[date('m', strtotime($fecha)) - 1] . " de " . date('Y', strtotime($fecha)));
}

function fecha_abr($fecha)
{
    $arrayMeses = array(
        'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
        'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
    );
    return date('d', strtotime($fecha)) . "/" . $arrayMeses[date('m', strtotime($fecha)) - 1] . "/" . date('Y', strtotime($fecha));
}

function dia($fecha)
{
    $arrayDias = array(
        'D', 'L', 'M',
        'M', 'J', 'V', 'S'
    );
    return ($arrayDias[date('w', strtotime($fecha))]);
}


function dia_numero($fecha)
{
    return date('d', strtotime($fecha));
}


function mes_from_fecha($fecha)
{
    $arrayMeses = array(
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    );
    return strtoupper(($arrayMeses[date('m', strtotime($fecha)) - 1]));
}

function mes_year_from_fecha($fecha)
{
    $arrayMeses = array(
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    );
    return strtoupper(($arrayMeses[date('m', strtotime($fecha)) - 1]) . "/" . date('Y', strtotime($fecha)));
}


function calculaedad($fechanacimiento)
{
    list($ano, $mes, $dia) = explode("-", $fechanacimiento);
    $ano_diferencia  = date("Y") - $ano;
    $mes_diferencia = date("m") - $mes;
    $dia_diferencia   = date("d") - $dia;
    if ($dia_diferencia < 0 || $mes_diferencia < 0)
        $ano_diferencia--;
    return $ano_diferencia;
}