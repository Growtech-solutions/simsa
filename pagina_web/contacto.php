
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectos</title>
    <link rel="stylesheet" href="Normalize.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="header__logo-titulo">
            <img class="header__logo" src="../img/logo.png" alt="logo SIMSA">
            <h1 class="header__titulo">Suministros Industriales Modernos</h1>
        </div>
    </header>
    <nav class="nav">
        <div class="nav__container">
            <a href="index.php" class="nav__link">INICIO</a>
            <a href="servicios.php" class="nav__link">SERVICIOS</a>
            <a href="contacto.php" class="nav__link nav__link--active">¿BUSCAS TRABAJO?</a>
        </div>
    </nav>
    <style>
        .header {
            background: var(--primario);
            color: #fff;
            padding: 1.5rem 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .header__logo-titulo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            max-width: 1100px;
            margin: 0 auto;
        }
        .header__logo {
            height: 60px;
            width: auto;
            border-radius: 8px;
            background: #fff;
            padding: 0.2rem;
        }
        .header__titulo {
            font-family: 'Staatliches', cursive;
            font-size: 2.2rem;
            letter-spacing: 1px;
            margin: 0;
        }
        .nav {
            background: var(--primario);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .nav__container {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            gap: 2.5rem;
            padding: 0.7rem 2rem;
        }
        .nav__link {
            color: #fff;
            text-decoration: none;
            font-size: 1.15rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            padding: 0.4rem 1.2rem;
            border-radius: 4px;
            transition: background 0.2s, color 0.2s, transform 0.2s;
        }
        .nav__link:hover,
        .nav__link--active {
            background: #fff;
            color: #005baa;
            transform: translateY(-2px) scale(1.05);
        }
        @media (max-width: 700px) {
            .header__logo-titulo {
                flex-direction: column;
                gap: 0.7rem;
            }
            .header__titulo {
                font-size: 1.3rem;
                text-align: center;
            }
            .nav__container {
                flex-direction: column;
                gap: 0.5rem;
                align-items: center;
                padding: 0.7rem 0.5rem;
            }
        }
    </style>
    
<br>

    <div class="Mecanico">
        <section class="quienes_somos">
            <nav class="somos">
                <h3>Mecanico</h3>
            </nav>
            <nav class="somos" >
                <img class="somos__imagen" src="img/maquinados.png" alt="">
            </nav>
            <nav class="somos__texto">
                <p>
                    Buscamos un Mecánico con más de 18 años de edad y experiencia en el mantenimiento y reparación de maquinaria industrial y equipos mecánicos. <br> 
                El candidato ideal deberá ser competente en la identificación y solución de problemas mecánicos, así como realizar ajustes y mejoras según sea necesario. <br>
                Se valorará la capacidad para trabajar de manera colaborativa y aprender nuevas técnicas y procedimientos.
                </p>
            </nav>
        </section>
    </div>
    <div class="Soldadura">
        <section class="quienes_somos">
            <nav class="somos">
                <h3>Soldador</h3>
            </nav>
            <nav class="somos" >
                <img class="somos__imagen" src="img/paileria.png" alt="">
            </nav>
            <nav class="somos__texto">
                <p>
                    Estamos buscando un Soldador hábil con más de 18 años de edad para unirse a nuestro equipo. <br>
                    El candidato ideal deberá tener experiencia en la soldadura de diversos metales y materiales, utilizando diferentes técnicas y equipos. <br>
                    Se espera que el Soldador trabaje con precisión, siguiendo especificaciones y estándares de seguridad. <br>
                    Se valorará la capacidad para interpretar planos y trabajar de manera autónoma o en equipo. 
                </p>
            </nav>
        </section>
    </div>
    <div class="Electricista">
        <section class="quienes_somos">
            <nav class="somos">
                <h3>Electricista</h3>
            </nav>
            <nav class="somos" >
                <img class="somos__imagen" src="img/electricista.png" alt="">
            </nav>
            <nav class="somos__texto">
                <p>Estamos en búsqueda de un Electricista con más de 18 años de edad y experiencia en la instalación, mantenimiento y reparación de sistemas eléctricos industriales. <br>
                    Se espera que el candidato posea un conocimiento profundo de las normativas eléctricas, sea capaz de interpretar planos eléctricos y realice tareas de forma segura 
                    y eficiente. <br>
                    La capacidad para solucionar problemas eléctricos y trabajar en colaboración con otros profesionales será fundamental. </p>
            </nav>
        </section>
    </div>
    <div class="Ayudante general">
        <section class="quienes_somos">
            <nav class="somos">
                <h3>Ayudante general</h3>
            </nav>
            <nav class="somos" >
                <img class="somos__imagen" src="img/AyudanteGeneral.png" alt="">
            </nav>
            <nav class="somos__texto">
                <p>Buscamos un Ayudante General mayor de 18 años para desempeñar un papel fundamental en el soporte y ejecución de diversas tareas en nuestro entorno laboral. <br>
                    El candidato seleccionado colaborará en múltiples áreas, proporcionando asistencia en actividades como carga y descarga, limpieza, organización y otras tareas asignadas. <br>
                    No se requiere experiencia previa, pero se valora la actitud positiva, la disposición para aprender y la capacidad para trabajar en equipo. 
                </p>
            </nav>
        </section>
    </div>

    <main class="contenedor">
        <section>
            <h2>Contacto</h2>
        
            <form class="formulario" action="http://pub44.bravenet.com/emailfwd/senddata.php" method="POST">
                <input type="hidden" name="usernum" value="3706832267" />
                <input type="hidden" name="cpv" value="2" />
                <a name="top"></a>

                <fieldset>
                    <legend>Contactanos llenando los campos</legend>
                    <div class="contenedor-campos">
                        <div class="campo">
                            <label>Nombre</label>
                            <input class="input-text" type="text" placeholder="Tu nombre" name="nombre" required>
                        </div>
        
                        <div class="campo">
                            <label>Telefono</label>
                            <input class="input-text" type="tel" placeholder="Tu telefono" name="telefono" required>
                        </div>

                        <div class="campo">
                            <label>Edad</label>
                            <input class="input-text" type="number" placeholder="Tu edad" name="edad" required>
                        </div>
        
                        <div class="campo">
                            <label>Puesto que solicita</label>
                            <input class="input-text" type="text" placeholder="Puesto" name="puesto" required>
                        </div>

                        <div class="campo">
                            <label>Experiencia</label>
                            <textarea class="input-text"  placeholder="Mensaje" name="experiencia" required> </textarea>
                        </div>

                    </div>
                        
                    <div class="alinear-derecha flex">
                        <input class="boton w-sm-100" type="submit" value="Enviar" name="B1"></input>
                    </div>
        
                    
                </fieldset>
            </form>
        </section >
    </main>

    <footer class="footer">
            <div class="footer__contenedor">
            <div class="footer__info">
                <img class="footer__logo" src="../img/logo.png" alt="logo SIMSA" style="height:60px;">
                <p>© <?php echo date("Y"); ?> SIMSA - Suministros Industriales Modernos S.A. de C.V.</p>
                <p>AV. RAYMUNDO ALMAGUER 1700 COL ALMAGUER GPE. NL. CP 67180</p>
                <p>Tel: 8183600990</p>
            </div>
            <div class="footer__redes">
                <a href="#" title="X" aria-label="X">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-x" width="28" height="28" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M4 4l11.733 16h4.267l-11.733 -16z" />
                    <path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772" />
                </svg>
                </a>
                <a href="#" title="Facebook" aria-label="Facebook">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-facebook" width="28" height="28" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" />
                </svg>
                </a>
                <a href="#" title="LinkedIn" aria-label="LinkedIn">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-linkedin" width="28" height="28" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                    <path d="M8 11l0 5" />
                    <path d="M8 8l0 .01" />
                    <path d="M12 16l0 -5" />
                    <path d="M16 16v-3a2 2 0 0 0 -4 0" />
                </svg>
                </a>
            </div>
            </div>
        </footer>
        <style>
            .footer {
            background: #222;
            color: #fff;
            padding: 2rem 0 1rem 0;
            margin-top: 3rem;
            }
            .footer__contenedor {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
            padding: 0 2rem;
            }
            .footer__info {
            flex: 1 1 300px;
            min-width: 220px;
            }
            .footer__logo {
            display: block;
            margin-bottom: 1rem;
            }
            .footer__info p {
            margin: 0.2rem 0;
            font-size: 1rem;
            }
            .footer__redes {
            display: flex;
            gap: 1rem;
            align-items: center;
            }
            .footer__redes a {
            display: inline-flex;
            align-items: center;
            transition: transform 0.2s;
            }
            .footer__redes a:hover {
            transform: scale(1.15);
            }
            @media (max-width: 700px) {
            .footer__contenedor {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .footer__redes {
                margin-top: 1rem;
            }
            }
        </style>
    
</body>
</html>