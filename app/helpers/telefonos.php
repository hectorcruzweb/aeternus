<?php
/*----Inicio formato_numero_telefono ---------*/
function formato_numero_telefono($num_tlf)
{
    // Separa en grupos de tres  
    $num_tlfn = chunk_split($num_tlf, 3, " ");

    // Creamos un grupo de 3 digitos y tres grupos de 2 digitos  
    $num_tlf1 = substr($num_tlf, 0, 3);
    $num_tlf2 = substr($num_tlf, 3, 3);
    $num_tlf3 = substr($num_tlf, 6, 3);


    $num_tlfno = "$num_tlf1 $num_tlf2 $num_tlf3";
    return $num_tlfno;
}


/*----FIN  formato_numero_telefono ---------*/