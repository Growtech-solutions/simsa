<?php
    switch (true) {
    case ($percepciones_gravadas >= 0.01 && $percepciones_gravadas <= 416.7):
        $limite_inferior = 0.01;
        $limite_superior = 416.7;
        $cuota_fija = 0.00;
        $porcentaje_excedente = 1.92;
        break;

    case ($percepciones_gravadas >= 416.71 && $percepciones_gravadas <= 3537.15):
        $limite_inferior = 416.71;
        $limite_superior = 3537.15;
        $cuota_fija = 7.95;
        $porcentaje_excedente = 6.40;
        break;

    case ($percepciones_gravadas >= 3537.16 && $percepciones_gravadas <= 6216.15):
        $limite_inferior = 3537.16;
        $limite_superior = 6216.15;
        $cuota_fija = 207.75;
        $porcentaje_excedente = 10.88;
        break;

    case ($percepciones_gravadas >= 6216.16 && $percepciones_gravadas <= 7225.95):
        $limite_inferior = 6216.16;
        $limite_superior = 7225.95;
        $cuota_fija = 499.20;
        $porcentaje_excedente = 16.00;
        break;

    case ($percepciones_gravadas >= 7225.96 && $percepciones_gravadas <= 8651.40):
        $limite_inferior = 7225.96;
        $limite_superior = 8651.40;
        $cuota_fija = 660.75;
        $porcentaje_excedente = 17.92;
        break;

    case ($percepciones_gravadas >= 8651.41 && $percepciones_gravadas <= 17448.75):
        $limite_inferior = 8651.41;
        $limite_superior = 17448.75;
        $cuota_fija = 916.20;
        $porcentaje_excedente = 21.36;
        break;

    case ($percepciones_gravadas >= 17448.76 && $percepciones_gravadas <= 27501.60):
        $limite_inferior = 17448.76;
        $limite_superior = 27501.60;
        $cuota_fija = 2795.25;
        $porcentaje_excedente = 23.52;
        break;

    case ($percepciones_gravadas >= 27501.61 && $percepciones_gravadas <= 52505.25):
        $limite_inferior = 27501.61;
        $limite_superior = 52505.25;
        $cuota_fija = 5159.70;
        $porcentaje_excedente = 30.00;
        break;

    case ($percepciones_gravadas >= 52505.26 && $percepciones_gravadas <= 70006.95):
        $limite_inferior = 52505.26;
        $limite_superior = 70006.95;
        $cuota_fija = 12660.75;
        $porcentaje_excedente = 32.00;
        break;

    case ($percepciones_gravadas >= 70006.96 && $percepciones_gravadas <= 210020.70):
        $limite_inferior = 70006.96;
        $limite_superior = 210020.70;
        $cuota_fija = 18261.30;
        $porcentaje_excedente = 34.00;
        break;

    case ($percepciones_gravadas >= 210020.71):
        $limite_inferior = 210020.71;
        $limite_superior = PHP_INT_MAX;
        $cuota_fija = 65886.05;
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