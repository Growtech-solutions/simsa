<?php
    switch (true) {
    case ($percepciones_gravadas >= 0.01 && $percepciones_gravadas <= 368.10):
        $limite_inferior = 0.01;
        $limite_superior = 368.10;
        $cuota_fija = 0.00;
        $porcentaje_excedente = 1.92;
        break;

    case ($percepciones_gravadas >= 368.11 && $percepciones_gravadas <= 3124.35):
        $limite_inferior = 368.11;
        $limite_superior = 3124.35;
        $cuota_fija = 7.05;
        $porcentaje_excedente = 6.40;
        break;

    case ($percepciones_gravadas >= 3124.36 && $percepciones_gravadas <= 5490.75):
        $limite_inferior = 3124.36;
        $limite_superior = 5490.75;
        $cuota_fija = 183.45;
        $porcentaje_excedente = 10.88;
        break;

    case ($percepciones_gravadas >= 5490.76 && $percepciones_gravadas <= 6382.80):
        $limite_inferior = 5490.76;
        $limite_superior = 6382.80;
        $cuota_fija = 441.00;
        $porcentaje_excedente = 16.00;
        break;

    case ($percepciones_gravadas >= 6382.81 && $percepciones_gravadas <= 7641.90):
        $limite_inferior = 6382.81;
        $limite_superior = 7641.90;
        $cuota_fija = 583.65;
        $porcentaje_excedente = 17.92;
        break;

    case ($percepciones_gravadas >= 7641.91 && $percepciones_gravadas <= 15412.80):
        $limite_inferior = 7641.91;
        $limite_superior = 15412.80;
        $cuota_fija = 809.25;
        $porcentaje_excedente = 21.36;
        break;

    case ($percepciones_gravadas >= 15412.81 && $percepciones_gravadas <= 24292.65):
        $limite_inferior = 15412.81;
        $limite_superior = 24292.65;
        $cuota_fija = 2469.15;
        $porcentaje_excedente = 23.52;
        break;

    case ($percepciones_gravadas >= 24292.66 && $percepciones_gravadas <= 46378.50):
        $limite_inferior = 24292.66;
        $limite_superior = 46378.50;
        $cuota_fija = 4557.75;
        $porcentaje_excedente = 30.00;
        break;

    case ($percepciones_gravadas >= 46378.51 && $percepciones_gravadas <= 61838.10):
        $limite_inferior = 46378.51;
        $limite_superior = 61838.10;
        $cuota_fija = 11183.40;
        $porcentaje_excedente = 32.00;
        break;

    case ($percepciones_gravadas >= 61838.11 && $percepciones_gravadas <= 185514.30):
        $limite_inferior = 61838.11;
        $limite_superior = 185514.30;
        $cuota_fija = 16130.55;
        $porcentaje_excedente = 34.00;
        break;

    case ($percepciones_gravadas >= 185514.31):
        $limite_inferior = 185514.31;
        $limite_superior = PHP_INT_MAX;
        $cuota_fija = 58180.35;
        $porcentaje_excedente = 35.00;
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

    // Subsidio
    if ($percepciones_gravadas < 4540.5) {
        $subsidio = ($uma / 100 * 11.82) * 15;
    } else {
        $subsidio = 0;
    }
    if ($subsidio > $isr_a_cargo) {
        $subsidio = $isr_a_cargo;
    }
    $isr_retencion = round($isr_a_cargo - $subsidio, 2);