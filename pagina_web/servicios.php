<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios</title>
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
            <a href="servicios.php" class="nav__link nav__link--active">SERVICIOS</a>
            <a href="contacto.php" class="nav__link">¿BUSCAS TRABAJO?</a>
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
    <main class="contenedor sombra">
        <h2>Mis servicios</h2>
        <div class="servicios">
            <section class="servicio">
                <h3>Diseño</h3>

                <div class="iconos">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-palette" width="56" height="56" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 21a9 9 0 0 1 0 -18c4.97 0 9 3.582 9 8c0 1.06 -.474 2.078 -1.318 2.828c-.844 .75 -1.989 1.172 -3.182 1.172h-2.5a2 2 0 0 0 -1 3.75a1.3 1.3 0 0 1 -1 2.25" />
                        <path d="M8.5 10.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M12.5 7.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M16.5 10.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                      </svg>
                </div>

                <p> 
                    Brindanos soluciones innovadoras y eficientes para tus proyectos.<br>
                    Nuestro equipo de expertos se dedica a transformar ideas en realidades tangibles. <br><br>
                    Nuestros servicios de Diseño Mecánico incluyen:<br>
                </p>
                
                <p class="servicio__texto" id="diseño-content">
                    
                    <b>Conceptualización:</b> Trabajamos de cerca contigo para transformar conceptos en diseños tangibles y funcionales. <br>
                        
                    <b>Modelado en 3D:</b>Utilizamos herramientas de modelado en 3D para crear representaciones virtuales precisas de tus diseños, permitiéndote visualizar y perfeccionar cada detalle antes de la producción. <br>
                        
                    <b>Documentación Técnica:</b> Generamos documentación técnica detallada, incluyendo planos y especificaciones, para facilitar el proceso de fabricación y montaje. <br>
                </p>  
                
                <div class="flecha" onclick="toggleDesplegable('diseño-content')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-down" width="36" height="36" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14" />
                        <path d="M18 13l-6 6" />
                        <path d="M6 13l6 6" />                            
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-up" width="36" height="36" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14" />
                        <path d="M18 11l-6 -6" />
                        <path d="M6 11l6 -6" />
                      </svg>
                </div>
            </section>

            <section class="servicio" >
                <h3>Maquinados</h3>
    
                <div class="iconos" >
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="56" height="56" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                        <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                    </svg>
                </div>
    
                <div>
                    <p> 
                        Fabricamos productos de alta precisión que nos permite dar forma y dimensiones exactas a diversos materiales, como metales, plásticos y más. <br><br>
        
                        Nuestros servicios de maquinado incluyen: 
                        <p class="servicio__texto" id="maquinados-content">        
                            <b>Torneado:</b> Proceso para dar forma a la pieza mediante la rotación de la misma.<br>
                            <b>Fresado:</b> Corte y modelado de la pieza utilizando una herramienta giratoria.<br>
                            <b>Taladrado:</b> Creación de agujeros precisos según las especificaciones requeridas.<br>
                            <b>Rectificado:</b> Obtención de superficies lisas y acabados de alta calidad.<br>
                                Contamos con tecnología de punta y un equipo altamente capacitado para garantizar la calidad y precisión en cada proyecto. 
                        </p>
                    </p>
                </div>

                <div class="flecha" onclick="toggleDesplegable('maquinados-content')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-down" width="36" height="36" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14" />
                        <path d="M18 13l-6 6" />
                        <path d="M6 13l6 6" />                           
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-up" width="36" height="36" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14" />
                        <path d="M18 11l-6 -6" />
                        <path d="M6 11l6 -6" />
                      </svg>
                </div>
                
            </section>

            <section class="servicio">
                <h3>Paileria</h3>

                <div class="iconos">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chisel" width="36" height="36" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M14 14l1.5 1.5" />
                        <path d="M18.347 15.575l2.08 2.079a1.96 1.96 0 0 1 -2.773 2.772l-2.08 -2.079a1.96 1.96 0 0 1 2.773 -2.772z" />
                        <path d="M3 6l3 -3l7.414 7.414a2 2 0 0 1 .586 1.414v2.172h-2.172a2 2 0 0 1 -1.414 -.586l-7.414 -7.414z" />
                    </svg>
                </div>

                <p>
                    Presentamos soluciones integrales especializadas en la fabricación y montaje de estructuras metálicas y componentes, ofreciendo soluciones versátiles y robustas para diversas aplicaciones. <br><br>

                    Nuestros servicios de pailería abarcan:
                </p>
                <p class="servicio__texto" id="paileria-content">
                    <b>Diseño y Fabricación:</b> Desarrollamos proyectos a medida, desde la conceptualización hasta la fabricación de estructuras metálicas adaptadas a tus necesidades particulares. <br>
                    
                    <b>Soldadura Profesional:</b> Contamos con expertos en soldadura que garantizan uniones sólidas y duraderas, utilizando las técnicas más avanzadas y los materiales más adecuados. <br>
                    
                    <b>Reparación y Mantenimiento:</b>Ofrecemos servicios de reparación y mantenimiento para prolongar la vida útil de las estructuras metálicas existentes. <br>
                </p>

                <div class="flecha" onclick="toggleDesplegable('paileria-content')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-down" width="36" height="36" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14" />
                        <path d="M18 13l-6 6" />
                        <path d="M6 13l6 6" />                           
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-up" width="36" height="36" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14" />
                        <path d="M18 11l-6 -6" />
                        <path d="M6 11l6 -6" />
                      </svg>
                </div>        
            </section>

              
            </section>

            
        </div>

        <section class="servicios2">
            <section class="servicio">
                <h3>Instalaciones</h3>
                <div class="iconos">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck" width="56" height="56" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                        <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                        <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                      </svg>
                </div>

                <p>
                    Nuestra empresa se enorgullece de ofrecer servicios integrales de instalaciones, 
                    proporcionando soluciones eficientes y profesionales para cubrir tus necesidades. 
                    nuestro equipo altamente capacitado se encarga de cada proyecto con dedicación y experiencia.
                </p>        
            </section>
            <section class="servicio">
                <h3>Mantenimiento</h3>

                <div class="iconos">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-checkup-list" width="56" height="56" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                        <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                        <path d="M9 14h.01" />
                        <path d="M9 17h.01" />
                        <path d="M12 16l1 1l3 -3" />
                    </svg>
                </div>

                <p>
                    En nuestra empresa, entendemos la importancia del mantenimiento para garantizar el rendimiento óptimo y la durabilidad de tus equipos y sistemas. 
                    Nuestros servicios de mantenimiento están diseñados para cubrir una amplia gama de necesidades, desde maquinaria industrial hasta instalaciones comerciales, 
                    asegurando un funcionamiento continuo y eficiente.
                </p>   
        </section>

           
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
    

    <script>
        function toggleDesplegable(id) {
            var desplegable = document.getElementById(id);
            desplegable.classList.toggle("show");
        }
    </script>
</body>
</html>

