<?php /* ISR tablas */
switch (true) {
    case ($percepciones_gravadas >= 0.01 && $percepciones_gravadas <= 171.78):
        $limite_inferior = 0.01;
        $limite_superior = 171.78;
        $cuota_fija = 0;
        $porcentaje_excedente = 1.92;
        break;
    case ($percepciones_gravadas >= 171.79 && $percepciones_gravadas <= 1458.03):
        $limite_inferior = 171.79;
        $limite_superior = 1458.03;
        $cuota_fija = 3.29;
        $porcentaje_excedente = 6.40;
        break;
    case ($percepciones_gravadas >= 1458.04 && $percepciones_gravadas <= 2562.35):
        $limite_inferior = 1458.04;
        $limite_superior = 2562.35;
        $cuota_fija = 85.61;
        $porcentaje_excedente = 10.88;
        break;
    case ($percepciones_gravadas >= 2562.36 && $percepciones_gravadas <= 2978.63):
        $limite_inferior = 2562.36;
        $limite_superior = 2978.63;
        $cuota_fija = 205.8;
        $porcentaje_excedente = 16;
        break;
    case ($percepciones_gravadas >= 2978.64 && $percepciones_gravadas <= 3566.22):
        $limite_inferior = 2978.64;
        $limite_superior = 3566.22;
        $cuota_fija = 272.37;
        $porcentaje_excedente = 17.92;
        break;
    case ($percepciones_gravadas >= 3566.23 && $percepciones_gravadas <= 7192.64):
        $limite_inferior = 3566.23;
        $limite_superior = 7192.64;
        $cuota_fija = 377.65;
        $porcentaje_excedente = 21.36;
        break;
    case ($percepciones_gravadas >= 7192.65 && $percepciones_gravadas <= 11336.57):
        $limite_inferior = 7192.65;
        $limite_superior = 11336.57;
        $cuota_fija = 1152.27;
        $porcentaje_excedente = 23.52;
        break;
    case ($percepciones_gravadas >= 11336.58 && $percepciones_gravadas <= 14307.55):
        $limite_inferior = 11336.58;
        $limite_superior = 14307.55;
        $cuota_fija = 2674.94;
        $porcentaje_excedente = 30;
        break;
    case ($percepciones_gravadas >= 14307.56 && $percepciones_gravadas <= 21643.3):
        $limite_inferior = 14307.56;
        $limite_superior = 21643.3;
        $cuota_fija = 3884.2;
        $porcentaje_excedente = 32;
        break;
    case ($percepciones_gravadas >= 21643.31 && $percepciones_gravadas <= 28857.78):
        $limite_inferior = 21643.31;
        $limite_superior = 28857.78;
        $cuota_fija = 5218.92;
        $porcentaje_excedente = 34;
        break;
    case ($percepciones_gravadas >= 28857.79 && $percepciones_gravadas <= 86573.35):
        $limite_inferior = 28857.79;
        $limite_superior = 86573.35;
        $cuota_fija = 7527.59;
        $porcentaje_excedente = 34;
        break;
    case ($percepciones_gravadas >= 86573.36):
        $limite_inferior = 86573.36;
        $limite_superior = PHP_INT_MAX;
        $cuota_fija = 27150.83;
        $porcentaje_excedente = 35;
        break;
    default:
        $limite_inferior = 0;
        $limite_superior = 0;
        $cuota_fija = 0;
        $porcentaje_excedente = 0;
        break;
}
    $excedente_limite = $percepciones_gravadas - $limite_inferior;
    $isr_excedente = $excedente_limite / 100 * $porcentaje_excedente;
    $isr_a_cargo = $isr_excedente + $cuota_fija;

    //Limite para subsidio 
    $limite_sub = 10171/30.4*7;

    // Subsidio
    if ($percepciones_gravadas < $limite_sub) {
        $subsidio = (($uma * .138)) * 7;
    } else {
        $subsidio = 0;
    }
    if ($subsidio > $isr_a_cargo) {
        $subsidio = $isr_a_cargo;
    }
    $isr_retencion = round($isr_a_cargo - $subsidio, 2);
?>