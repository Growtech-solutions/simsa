<?php /* ISR tablas */
switch (true) {
    case ($percepciones_gravadas >= 0.01 && $percepciones_gravadas <= 194.46):
        $limite_inferior = 0.01;
        $limite_superior = 194.46;
        $cuota_fija = 0;
        $porcentaje_excedente = 1.92;
        break;
    case ($percepciones_gravadas >= 194.47 && $percepciones_gravadas <= 1650.67):
        $limite_inferior = 194.47;
        $limite_superior = 1650.67;
        $cuota_fija = 3.71;
        $porcentaje_excedente = 6.4;
        break;
    case ($percepciones_gravadas >= 1650.68 && $percepciones_gravadas <= 2900.87):
        $limite_inferior = 1650.68;
        $limite_superior = 2900.87;
        $cuota_fija = 96.95;
        $porcentaje_excedente = 10.88;
        break;
    case ($percepciones_gravadas >= 2900.88 && $percepciones_gravadas <= 3372.11):
        $limite_inferior = 2900.88;
        $limite_superior = 3372.11;
        $cuota_fija = 232.96;
        $porcentaje_excedente = 16;
        break;
    case ($percepciones_gravadas >= 3372.12 && $percepciones_gravadas <= 4037.32):
        $limite_inferior = 3372.12;
        $limite_superior = 4037.32;
        $cuota_fija = 308.35;
        $porcentaje_excedente = 17.92;
        break;
    case ($percepciones_gravadas >= 4037.33 && $percepciones_gravadas <= 8142.75):
        $limite_inferior = 4037.33;
        $limite_superior = 8142.75;
        $cuota_fija = 427.56;
        $porcentaje_excedente = 21.36;
        break;
    case ($percepciones_gravadas >= 8142.76 && $percepciones_gravadas <= 12834.08):
        $limite_inferior = 8142.76;
        $limite_superior = 12834.08;
        $cuota_fija = 1304.45;
        $porcentaje_excedente = 23.52;
        break;
    case ($percepciones_gravadas >= 12834.09 && $percepciones_gravadas <= 24502.45):
        $limite_inferior = 12834.09;
        $limite_superior = 24502.45;
        $cuota_fija = 2407.86;
        $porcentaje_excedente = 30;
        break;
    case ($percepciones_gravadas >= 24502.46 && $percepciones_gravadas <= 32669.91):
        $limite_inferior = 24502.46;
        $limite_superior = 32669.91;
        $cuota_fija = 5908.35;
        $porcentaje_excedente = 32;
        break;
    case ($percepciones_gravadas >= 32669.92 && $percepciones_gravadas <= 98009.66):
        $limite_inferior = 32669.92;
        $limite_superior = 98009.66;
        $cuota_fija = 8521.94;
        $porcentaje_excedente = 34;
        break;
    case ($percepciones_gravadas >= 98009.67):
        $limite_inferior = 98009.67;
        $limite_superior = PHP_INT_MAX;
        $cuota_fija = 30737.49;
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
    $isr_excedente = $excedente_limite * $porcentaje_excedente/ 100 ;
    $isr_a_cargo = $isr_excedente + $cuota_fija;

    //Limite para subsidio 
    $limite_sub = 11492.66/30.4*7;

    // Subsidio
    if ($percepciones_gravadas < $limite_sub) {
        $subsidio = (($uma * .1502)) * 7;
    } else {
        $subsidio = 0;
    }
    if ($subsidio > $isr_a_cargo) {
        $subsidio = $isr_a_cargo;
    }
    $isr_retencion = round($isr_a_cargo - $subsidio, 2);
?>