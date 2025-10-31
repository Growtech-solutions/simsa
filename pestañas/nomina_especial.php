<?php 
//BAJA
                        $fechaIngreso = new DateTime($fila['fecha_ingreso']); // Fecha de ingreso desde la base de datos
                        $fechaHoy = new DateTime(); // Fecha actual (hoy)
                        $diferencia = $fechaIngreso->diff($fechaHoy); // Calcula la diferencia
                        $diasLaborados = $diferencia->days;
                        $finiquito = (($vacaciones-$fila['vacaciones_usadas']) * (1.25 * $sdi)) + (($diasLaborados / 365) * ($sdi * 15));
                        $sql_bajas = "SELECT * FROM bajas WHERE fecha >= '$fecha_inicial' AND id_trabajador = $id_trabajador";
                        $resultado_bajas = mysqli_query($conexion, $sql_bajas);
                        if (mysqli_num_rows($resultado_bajas) > 0) {
                            $fila_bajas = mysqli_fetch_assoc($resultado_bajas);
                            if ($fila_bajas['causa']=="despido_injustificado") {
                                $pago_baja = $finiquito+($sdi*90);
                            } else {
                                $pago_baja = $finiquito; 
                            }
                        } else {
                            $pago_baja = 0; // o algún valor predeterminado
                        }

                        //aguinaldo
                        $fecha_aguinaldo = '2024-12-13';
                        if($fecha_inicial<=$fecha_aguinaldo && $fecha_final>=$fecha_aguinaldo){
                            //$aguinaldo=$sdi*15;
                            $aguinaldo=0;
                        }
                        else{
                                $aguinaldo=0;
                            }

                        $ptu=0;
if ($pago_baja>(90*$uma)){
                            $baja_gravada=$pago_baja-(90*$uma);
                            $baja_excenta=(90*$uma);
                        }else{
                            $baja_gravada=0;
                            $baja_excenta=$pago_baja;
                        }
                        if ($aguinaldo>(30*$uma)){
                            $aguinaldo_gravado=$aguinaldo-(30*$uma);
                            $aguinaldo_excento=(30*$uma);
                        }else{
                            $aguinaldo_gravado=0;
                            $aguinaldo_excento=$aguinaldo;
                        }
                        if ($ptu>(15*$uma)){
                            $ptu_gravado=$ptu-(15*$uma);
                            $ptu_excento=(15*$uma);
                        }else{
                            $ptu_gravado=0;
                            $ptu_excento=$ptu;
                        }