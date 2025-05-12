<?php
//phpinfo(); die;
/**
 * @var \App\Modules\Shared\Infrastructure\Components\TplReader $this
 */
use App\Modules\Shared\Infrastructure\Components\Hasher\Hasher;
use App\Modules\Shared\Infrastructure\Components\Requester;
use App\Modules\Shared\Infrastructure\Views\Helpers\MessagesHelper;
use App\Modules\Shared\Infrastructure\Components\Sessioner;
use App\Modules\Shared\Infrastructure\Repositories\Configuration\EnvironmentRawRepository;

$form = $form ?? Sessioner::getInstance()->getOnce("form") ?? [];

$messageHelper = MessagesHelper::fromPrimitives([
    "errors" => [$error ?? ""],
    "success" => [$success ?? ""],
]);
$requester = Requester::getInstance();

$tokenOrg = [
    "uuid" => uniqid(),
    "source" => "email",
    "user_id" => "user_id",
    "ip" => $requester->getRequestIP(),
    "language" => $requester->getLanguage(),
    "os" => $requester->getOS(),
    "browser" => $requester->getBrowser(),
    "browser_version" => $requester->getBrowserVersion(),
    "date" => date("Y-m-d"),
    "time" => date("H:i:s"),
];

$hasher = Hasher::fromPrimitives([
    "encryptSalt" => EnvironmentRawRepository::getInstance()->getAppKey(),
]);;
$token = $hasher->getPackedToken($tokenOrg);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Arquiforma Construcción - Transformamos espacios, creamos historias</title>

    <meta name="description" content="Empresa especializada en reformas integrales, rehabilitación de edificios y diseño de interiores con un enfoque moderno, funcional y de alta calidad.">
    <meta name="keywords" content="reformas, construcción, rehabilitación, edificios, diseño de interiores, accesibilidad, aislamientos, proyectos de construcción">
    <meta name="author" content="Arquiforma">

    <meta rel="canonical" href="https://www.arquiforma.es/">

    <link rel="icon" type="image/png" href="/tpl/assets/images/favicon.svg">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">

    <link rel="stylesheet" href="/tpl/assets/css/tw-4.1.5.css">

    <link rel="stylesheet" href="/tpl/assets/css/global.css">
    <link rel="stylesheet" href="/tpl/assets/css/home.css">

</head>
<body class="bg-white">
<a href="#main-content" class="skip-link">Saltar al contenido principal</a>

<header id="header-menu" class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <a href="https://arquiforma.es" class="flex items-center" aria-label="Arquiforma Construcción - Página de inicio">
            <img src="/tpl/assets/images/arq-logo-h.svg" alt="Arquiforma Logo">
        </a>

        <nav id="nav-main-menu" class="hidden md:flex space-x-6 text-dark-gray font-semibold">
            <a href="#section-projects" class="hover:text-arquiforma-red">Proyectos</a>
            <a href="#section-services" class="hover:text-arquiforma-red">Servicios</a>
            <a href="#section-contact" class="hover:text-arquiforma-red">Contacto</a>
        </nav>

        <button id="button-hamburger" class="md:hidden focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div id="div-mobile-menu" class="md:hidden hidden px-4 pb-4">
        <nav class="flex flex-col space-y-2 text-dark-gray font-semibold">
            <a href="#section-projects" class="block py-2 px-4 rounded hover:bg-gray-100 hover:text-arquiforma-red">Proyectos</a>
            <a href="#section-services" class="block py-2 px-4 rounded hover:bg-gray-100 hover:text-arquiforma-red">Servicios</a>
            <a href="#section-contact" class="block py-2 px-4 rounded hover:bg-gray-100 hover:text-arquiforma-red">Contacto</a>
        </nav>
    </div>
</header>

<main id="main-content">
    <section id="section-jumbo" class="container mx-auto px-4 py-12 md:py-16 lg:py-24">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div id="div-slogan" class="relative order-2 md:order-1 lg:pl-12 md:pl-3 sm:pl-0">
                <div class="mb-4">
                    <p class="text-arqui-red uppercase tracking-wider">
                        <span class="font-arqui-poppins-black font-bold text-sm">Innovación, confianza y calidad</span>
                    </p>
                </div>
                <h1 class="font-arqui-roboto-black leading-tight mb-6 text-7xl">
                    Transformamos espacios, creamos historias
                </h1>
                <p class="text-gray-600 mb-8">
                    Arquiforma es más que una empresa de reformas. Somos especialistas en renovar espacios con un enfoque moderno,
                    funcional y lleno de significado. Nuestro compromiso es hacer realidad tus ideas, respetando la esencia de cada
                    lugar y aportando calidad en cada detalle del proyecto.
                </p>
                <a id="a-contact" href="#section-contact"
                   class="bg-arqui-red inline-block text-white uppercase px-6 sm:px-8 py-3 text-sm font-medium hover:bg-red-700 transition-colors duration-300">
                    Contáctanos
                </a>
                <div id="div-red-triangle" class="absolute -bottom-8 -left-4 triangle-corner hidden sm:block"></div>
            </div>
            <div id="div-images-animation" class="relative order-1 md:order-2">
                <div class="grid grid-cols-1 gap-4">
                    <div class="relative">
                        <img
                                src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2000&q=80"
                                alt="Interior de diseño moderno con materiales de alta calidad"
                                class="w-full h-64 sm:h-80 object-cover rounded-sm"
                                loading="lazy"
                        >
                        <div class="absolute bottom-0 left-0 bg-white p-2">
                            <p class="text-xs font-medium">MATERIALES DE CALIDAD</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- image-slider -->
    <style>
        .slider-container {
            position: relative;
            overflow: hidden;
        }

        .slide-group {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: none;
        }

        .slide-group.active {
            opacity: 1;
            position: relative;
            display: grid;
        }

        /* Corner mask effect */
        .corner-mask-tl::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 30px;
            height: 30px;
            background-color: white;
            clip-path: polygon(0 0, 0% 100%, 100% 0);
            z-index: 1;
        }

        .corner-mask-br::after {
            content: "";
            position: absolute;
            bottom: 0;
            right: 0;
            width: 32px; /* Increased size to cover any thin lines */
            height: 32px; /* Increased size to cover any thin lines */
            background-color: white;
            clip-path: polygon(100% 100%, 0% 100%, 100% 0);
            z-index: 1;
        }

        /* Progress bar animation */
        @keyframes progressAnimation {
            from { width: 0; }
            to { width: 100%; }
        }

        .progress-bar-inner {
            height: 4px;
            background-color: #e60012;
            width: 0;
            transition: width 0.3s ease-in-out;
        }

        /* Fade animation */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        /* Accessibility focus styles */
        button:focus {
            outline: 2px solid #e60012;
            outline-offset: 2px;
        }

        /* Skip to content link for accessibility */
        .skip-link {
            position: absolute;
            top: -40px;
            left: 0;
            background: #e60012;
            color: white;
            padding: 8px;
            z-index: 100;
            transition: top 0.3s;
        }

        .skip-link:focus {
            top: 0;
        }

        /* Screen reader only class */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        /* Custom button styles */
        .arrow-view-button {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            transition: transform 0.3s ease;
        }

        .arrow-view-button:hover {
            transform: translateX(3px);
        }
    </style>
    <div id="section-projects" class="container mx-auto px-4 py-8 mt-[50px]">
        <header id="header-slider-images">
            <div class="flex flex-col md:flex-row gap-8 items-center">
                <div class="w-full md:w-1/2">
                    <h2 class="font-arqui-roboto-bold text-5xl">
                        Profesionalidad y experiencia en cada proyecto.
                    </h2>
                </div>
                <div class="w-full md:w-1/2">
                    <p class="font-arqui-poppins text-lg font-bold">
                        En Arquiforma garantizamos calidad, innovación y atención al detalle en cada obra.
                        Nuestro equipo experto y el uso de materiales de alta calidad nos permiten ofrecer soluciones
                        seguras y adaptadas a cada cliente.
                    </p>
                </div>
            </div>
        </header>

        <section id="section-slider-images" class="mt-[75px]">
            <div class="slider-container relative">
                <div class="slide-group active fade-in grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Image 1 -->
                    <div class="relative overflow-hidden corner-mask-tl corner-mask-br group">
                        <img src="https://images.unsplash.com/photo-1503387762-592deb58ef4e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2000&q=80"
                             alt="Reformas integrales - Interior de oficina moderna"
                             class="w-full h-80 object-cover transition-transform duration-300 group-hover:scale-105">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                            <h3 class="font-arqui-roboto  text-white text-lg font-bold mb-1">Reformas integrales</h3>
                            <p class="text-white text-xs">Instalación de ascensores exteriores en edificios antiguos, mejorando accesibilidad y comodidad sin comprometer la estética.</p>
                        </div>
                        <button class="arrow-view-button" aria-label="Ver siguiente grupo">
                            <svg width="88" height="67">
                                <use xlink:href="/tpl/assets/images/sprite.svg#arrow-view"/>
                            </svg>
                        </button>
                    </div>

                    <div class="relative overflow-hidden corner-mask-tl corner-mask-br group">
                        <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2000&q=80"
                             alt="Accesibilidad a edificios - Fachada con ascensor exterior"
                             class="w-full h-80 object-cover transition-transform duration-300 group-hover:scale-105">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                            <h3 class="font-arqui-roboto  text-white text-lg font-bold mb-1">Accesibilidad a edificios</h3>
                            <p class="text-white text-xs">Soluciones innovadoras para mejorar la accesibilidad en edificios antiguos y modernos.</p>
                        </div>
                        <button class="arrow-view-button" aria-label="Ver siguiente grupo">
                            <svg width="88" height="67">
                                <use xlink:href="/tpl/assets/images/sprite.svg#arrow-view"/>
                            </svg>
                        </button>
                    </div>

                    <div class="relative overflow-hidden corner-mask-tl corner-mask-br group">
                        <img src="https://images.unsplash.com/photo-1581858726788-75bc0f6a952d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2000&q=80"
                             alt="Instalación de aislamientos - Sistema de calefacción por suelo radiante"
                             class="w-full h-80 object-cover transition-transform duration-300 group-hover:scale-105">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                            <h3 class="font-arqui-roboto text-white text-lg font-bold mb-1">Instalación de aislamientos</h3>
                            <p class="text-white text-xs">Mejora de la eficiencia energética con sistemas de aislamiento térmico y acústico de última generación.</p>
                        </div>
                        <button class="arrow-view-button" aria-label="Ver siguiente grupo">
                            <svg width="88" height="67">
                                <use xlink:href="/tpl/assets/images/sprite.svg#arrow-view"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="slide-group grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    <div class="relative overflow-hidden corner-mask-tl corner-mask-br group">
                        <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2000&q=80"
                             alt="Rehabilitación de fachadas - Edificio restaurado"
                             class="w-full h-80 object-cover transition-transform duration-300 group-hover:scale-105">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                            <h3 class="font-arqui-roboto  text-white text-lg font-bold mb-1">Rehabilitación de fachadas</h3>
                            <p class="text-white text-xs">Restauración y modernización de fachadas manteniendo la esencia arquitectónica original.</p>
                        </div>
                        <button class="arrow-view-button" aria-label="Ver siguiente grupo">
                            <svg width="88" height="67">
                                <use xlink:href="/tpl/assets/images/sprite.svg#arrow-view"/>
                            </svg>
                        </button>
                    </div>

                    <div class="relative overflow-hidden corner-mask-tl corner-mask-br group">
                        <img src="https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2000&q=80"
                             alt="Diseño de interiores - Sala de estar moderna"
                             class="w-full h-80 object-cover transition-transform duration-300 group-hover:scale-105">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                            <h3 class="font-arqui-roboto  text-white text-lg font-bold mb-1">Diseño de interiores</h3>
                            <p class="text-white text-xs">Creación de espacios modernos y funcionales adaptados a las necesidades de cada cliente.</p>
                        </div>
                        <button class="arrow-view-button" aria-label="Ver siguiente grupo">
                            <svg width="88" height="67">
                                <use xlink:href="/tpl/assets/images/sprite.svg#arrow-view"/>
                            </svg>
                        </button>
                    </div>

                    <div class="relative overflow-hidden corner-mask-tl corner-mask-br group">
                        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2000&q=80"
                             alt="Cocinas modernas - Diseño de cocina integrada"
                             class="w-full h-80 object-cover transition-transform duration-300 group-hover:scale-105">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                            <h3 class="font-arqui-roboto  text-white text-lg font-bold mb-1">Cocinas modernas</h3>
                            <p class="text-white text-xs">Diseño e instalación de cocinas funcionales con materiales de alta calidad y últimas tecnologías.</p>
                        </div>
                        <button class="arrow-view-button" aria-label="Ver siguiente grupo">
                            <svg width="88" height="67">
                                <use xlink:href="/tpl/assets/images/sprite.svg#arrow-view"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div id="div-progress-bar" class="mt-6">
                <div class="w-full bg-gray-200 h-1">
                    <div id="progress-bar" class="progress-bar-inner"></div>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-500">Proyectos <span id="current-group">1</span> de <span id="total-groups">2</span></span>
                    <button id="next-group-btn" class="text-xs arqui-red font-medium flex items-center gap-1 hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </section>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Slider functionality
            const slideGroups = document.querySelectorAll('.slide-group');
            const nextGroupBtn = document.getElementById('next-group-btn');
            const verButtons = document.querySelectorAll('.arrow-view-button');
            const progressBar = document.getElementById('progress-bar');
            const currentGroupElement = document.getElementById('current-group');
            const totalGroupsElement = document.getElementById('total-groups');

            let currentGroup = 0;
            const totalGroups = slideGroups.length;

            // Set total groups count
            totalGroupsElement.textContent = totalGroups;

            // Update progress bar
            function updateProgressBar() {
                const progressPercentage = ((currentGroup + 1) / totalGroups) * 100;
                progressBar.style.width = `${progressPercentage}%`;
                currentGroupElement.textContent = currentGroup + 1;

                // Announce slide change for screen readers
                announceGroupChange(currentGroup);
            }

            // Show a specific slide group
            function goToGroup(groupIndex) {
                // Remove active class from all slide groups
                slideGroups.forEach(group => {
                    group.classList.remove('active');
                    group.classList.remove('fade-in');
                });

                // Add active class to current slide group
                slideGroups[groupIndex].classList.add('active');
                slideGroups[groupIndex].classList.add('fade-in');

                // Update current group counter and progress bar
                currentGroup = groupIndex;
                updateProgressBar();
            }

            // Go to next slide group
            function nextGroup() {
                const newIndex = (currentGroup + 1) % totalGroups;
                goToGroup(newIndex);
            }

            // Add click events to navigation buttons
            nextGroupBtn.addEventListener('click', nextGroup);

            // Add click events to all VER buttons
            verButtons.forEach(button => {
                button.addEventListener('click', nextGroup);
            });

            // Add keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowRight') {
                    nextGroup();
                } else if (e.key === 'ArrowLeft') {
                    const newIndex = (currentGroup - 1 + totalGroups) % totalGroups;
                    goToGroup(newIndex);
                }
            });

            // Create a live region for screen reader announcements
            function setupLiveRegion() {
                const liveRegion = document.createElement('div');
                liveRegion.setAttribute('aria-live', 'polite');
                liveRegion.setAttribute('aria-atomic', 'true');
                liveRegion.classList.add('sr-only');
                liveRegion.id = 'slider-live-region';
                document.body.appendChild(liveRegion);
            }

            // Announce group changes for accessibility
            function announceGroupChange(index) {
                const liveRegion = document.getElementById('slider-live-region');
                if (liveRegion) {
                    liveRegion.textContent = `Mostrando grupo de imágenes ${index + 1} de ${totalGroups}`;
                }
            }

            // Add touch swipe functionality
            let touchStartX = 0;
            let touchEndX = 0;

            const slider = document.querySelector('.slider-container');

            slider.addEventListener('touchstart', function(e) {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            slider.addEventListener('touchend', function(e) {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            }, { passive: true });

            function handleSwipe() {
                const swipeThreshold = 50; // Minimum distance for a swipe

                if (touchEndX < touchStartX - swipeThreshold) {
                    // Swipe left, go to next group
                    nextGroup();
                }

                if (touchEndX > touchStartX + swipeThreshold) {
                    // Swipe right, go to previous group
                    const newIndex = (currentGroup - 1 + totalGroups) % totalGroups;
                    goToGroup(newIndex);
                }
            }

            // Auto-advance groups (optional)
            let slideInterval;

            function startAutoSlide() {
                slideInterval = setInterval(nextGroup, 8000); // Change group every 8 seconds
            }

            function stopAutoSlide() {
                clearInterval(slideInterval);
            }

            // Pause auto-advance on hover or focus
            slider.addEventListener('mouseenter', stopAutoSlide);
            slider.addEventListener('mouseleave', startAutoSlide);
            slider.addEventListener('focusin', stopAutoSlide);
            slider.addEventListener('focusout', startAutoSlide);

            // Initialize slider
            setupLiveRegion();
            updateProgressBar();
            startAutoSlide();

            // Preload images for smoother transitions
            function preloadImages() {
                const allImages = document.querySelectorAll('.slide-group img');
                allImages.forEach(img => {
                    const src = img.getAttribute('src');
                    const preloadImg = new Image();
                    preloadImg.src = src;
                });
            }

            preloadImages();
        });
    </script>
    <!-- /image-slider -->
    <section id="section-services" class="container mx-auto pt-[75px]">
        <h2 class="font-arqui-roboto-black text-center mb-8 text-5xl">
            Nuestros servicios
        </h2>
        <p class="text-gray-600 text-center max-w-4xl mx-auto mb-12 md:mb-16">
            Cada proyecto es único y requiere un enfoque especializado. En Arquiforma ofrecemos una gama completa de
            servicios para transformar cualquier espacio, desde reformas integrales hasta diseño de interiores y dirección de obra.
            Nos enfocamos en la funcionalidad, la estética y la calidad, asegurando resultados que superan expectativas.
        </p>

        <div id="div-services-cards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <div class="bg-gray-100 p-6 md:p-8 rounded-sm hover:shadow-lg transition-shadow duration-300 border-l-4 border-black">
                <div class="mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 md:h-12 md:w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="font-arqui-roboto-bold text-xl font-bold text-dark-gray mb-4">Reformas integrales</h3>
                <p class="text-gray-600">
                    Renovamos espacios con soluciones que combinan diseño y eficiencia, optimizando viviendas y locales comerciales.
                </p>
            </div>

            <div class="bg-gray-100 p-6 md:p-8 rounded-sm hover:shadow-lg transition-shadow duration-300 border-l-4 border-black">
                <div class="mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 md:h-12 md:w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <h3 class="font-arqui-roboto-bold text-xl font-bold text-dark-gray mb-4">Gestión de proyectos</h3>
                <p class="text-gray-600">
                    Planificamos y coordinamos cada aspecto de la obra, optimizando tiempos, recursos y calidad para una ejecución eficiente.
                </p>
            </div>

            <div class="bg-gray-100 p-6 md:p-8 rounded-sm hover:shadow-lg transition-shadow duration-300 border-l-4 border-black">
                <div class="mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 md:h-12 md:w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3 class="font-arqui-roboto-bold text-xl font-bold text-dark-gray mb-4">Obras previas a instalaciones</h3>
                <p class="text-gray-600">
                    Mejoramos la accesibilidad en edificios antiguos con soluciones integradas que respetan su estética.
                </p>
            </div>

            <div class="bg-gray-100 p-6 md:p-8 rounded-sm hover:shadow-lg transition-shadow duration-300 border-l-4 border-black">
                <div class="mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 md:h-12 md:w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <h3 class="font-arqui-roboto-bold text-xl font-bold text-dark-gray mb-4">Dirección de obra</h3>
                <p class="text-gray-600">
                    Supervisamos y gestionamos cada fase del proyecto para garantizar precisión, cumplimiento de plazos y resultados.
                </p>
            </div>

            <div class="bg-gray-100 p-6 md:p-8 rounded-sm hover:shadow-lg transition-shadow duration-300 border-l-4 border-black">
                <div class="mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 md:h-12 md:w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                </div>
                <h3 class="font-arqui-roboto-bold text-xl font-bold text-dark-gray mb-4">Diseño de interiores</h3>
                <p class="text-gray-600">
                    Creamos ambientes armoniosos y modernos, adaptados a la personalidad y necesidades de cada cliente.
                </p>
            </div>

            <div class="bg-gray-100 p-6 md:p-8 rounded-sm hover:shadow-lg transition-shadow duration-300 border-l-4 border-black">
                <div class="mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 md:h-12 md:w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3 class="font-arqui-roboto-bold text-xl font-bold text-dark-gray mb-4">Rehabilitación de edificios</h3>
                <p class="text-gray-600">
                    Damos nueva vida a edificios con soluciones duraderas y sostenibles, mejorando su imagen y eficiencia.
                </p>
            </div>
        </div>
        <p class="text-center mt-12 text-gray-600 font-medium pb-5">Y MUCHO MÁS...</p>
    </section>

    <section id="section-contact" class="bg-gray-100 py-16 md:py-24">
        <div class="container mx-auto px-4">
            <?= $messageHelper->getMessages() ?>
            <h2 class="font-arqui-roboto-black text-2xl sm:text-3xl md:text-4xl font-bold text-center mb-12">
                Contacta con nosotros
            </h2>
            <div class="max-w-3xl mx-auto">
                <h3 id="h3-error" class="text-center mb-6 hidden" style="color:red">xxx</h3>
                <form id="form-contact" method="post" action="/contact/send-message"
                    class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-1">
                        <label for="text-name" class="block text-sm font-arqui-poppins-bold text-gray-700 mb-1">Nombre *</label>
                        <input type="text" id="text-name" name="name"
                               value="<?= $form["name"] ?? "" ?>"
                               required
                               placeholder="Ej. Juan López"
                               class="w-full px-3 py-2 border border-gray-300 rounded-sm
                                   focus:outline-none focus:ring-2 focus:ring-arqui-red focus:border-arqui-red"
                        >
                    </div>
                    <div class="md:col-span-1">
                        <label for="email" class="block text-sm font-arqui-poppins-bold text-gray-700 mb-1">Email *</label>
                        <input type="email" id="email" name="email"
                               value="<?= $form["email"] ?? "" ?>"
                               required
                               placeholder="Ej. juan-lopez@my.email.loc"
                               class="w-full px-3 py-2 border border-gray-300 rounded-sm
                                   focus:outline-none focus:ring-2 focus:ring-arqui-red focus:border-arqui-red">
                    </div>
                    <div class="md:col-span-1">
                        <label for="text-subject" class="block text-sm font-arqui-poppins-bold text-gray-700 mb-1">Asunto *</label>
                        <input type="text" id="text-subject" name="subject"
                               value="<?= $form["subject"] ?? "" ?>"
                               required
                               placeholder="Ej. Consulta sobre reforma nave industrial"
                               class="w-full px-3 py-2 border border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-arqui-red focus:border-arqui-red">
                    </div>
                    <div class="md:col-span-1">
                        <label for="phone" class="block text-sm font-arqui-poppins-bold text-gray-700 mb-1">Teléfono</label>
                        <input type="tel" id="phone" name="phone"
                               value="<?= $form["phone"] ?? "" ?>"
                               placeholder="Ej. +34 600 123 456"
                               class="w-full px-3 py-2 border border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-arqui-red focus:border-arqui-red">
                    </div>
                    <div class="md:col-span-2">
                        <label for="txa-message" class="block text-sm font-arqui-poppins-bold text-gray-700 mb-1">Mensaje *</label>
                        <textarea id="txa-message" name="message" required rows="5"
                                  placeholder="Ej. Hola, me gustaría saber más sobre vuestros servicios de reformas integrales."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-arqui-red focus:border-arqui-red"
                        ><?= $form["message"] ?? "" ?></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" id="button-submit"
                                class="w-full bg-arqui-red text-white px-6 py-3 uppercase text-sm font-medium hover:bg-red-700 transition-colors duration-300">
                            Enviar mensaje
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

</main>

<footer id="footer" class="bg-arqui-red text-white py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <a href="#" class="mb-6 md:mb-0" aria-label="Arquiforma Construcción - Página de inicio">
                <img src="/tpl/assets/images/arq-logo-h-white.svg">
            </a>
            <nav id="nav-footer-menu" class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-8 mb-6 md:mb-0">
                <a href="#section-projects" class="text-white hover:text-gray-200 uppercase text-sm font-medium transition-colors duration-300">
                    Proyectos
                </a>
                <a href="#section-services" class="text-white hover:text-gray-200 uppercase text-sm font-medium transition-colors duration-300">
                    Servicios
                </a>
                <a href="#section-contact" class="text-white hover:text-gray-200 uppercase text-sm font-medium transition-colors duration-300">
                    Contacto
                </a>
            </nav>
        </div>
        <div class="border-t border-white/20 pt-6 text-center text-sm">
            <p>© 2025 Arquiforma® Construcción</p>
        </div>
    </div>
</footer>

<button id="button-back-to-top"
        class="fixed bottom-4 right-4 bg-arqui-red text-white p-2 rounded-full shadow-lg opacity-0 invisible transition-all duration-300"
        aria-label="Volver arriba">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
    </svg>
</button>

<script>
function showError(message) {
    const errorElement = document.getElementById('h3-error');
    errorElement.innerText = message;
    errorElement.classList.remove('hidden');
}
function hideError() {
    const errorElement = document.getElementById('h3-error');
    errorElement.innerText = "";
    errorElement.classList.add('hidden');
}

function scrollToForm() {
    const hasMessages = <?= $messageHelper->hasMessages() ? "true" : "false" ?>;
    if (!hasMessages) return;

    const form = document.getElementById('section-contact');
    if (form) {
        form.scrollIntoView({ behavior: 'smooth' });
    }
}

function createTokenField() {
    const form = document.getElementById('form-contact');
    const tokenFieldId = 'hid-token';

    if (!document.getElementById(tokenFieldId)) {
        const hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.id = tokenFieldId;
        hiddenField.name = '_token';
        form.appendChild(hiddenField);
    }
}

function createTimezoneField() {
    const form = document.getElementById('form-contact');
    const tokenFieldId = 'hid-timezone';

    if (!document.getElementById(tokenFieldId)) {
        const hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.id = tokenFieldId;
        hiddenField.name = '_timezone';

        hiddenField.value = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
        form.appendChild(hiddenField);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const buttonHamburger = document.getElementById('button-hamburger');
    const divMobileMenu = document.getElementById('div-mobile-menu');

    buttonHamburger.addEventListener('click', () => {
        divMobileMenu.classList.toggle('hidden');
    });


    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                const headerOffset = 80;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Back to top button
    const backToTopButton = document.getElementById('button-back-to-top');

    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopButton.classList.remove('opacity-0', 'invisible');
            backToTopButton.classList.add('opacity-100', 'visible');
        } else {
            backToTopButton.classList.add('opacity-0', 'invisible');
            backToTopButton.classList.remove('opacity-100', 'visible');
        }
    });

    backToTopButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Form validation and submission
    createTokenField()
    createTimezoneField()
    scrollToForm()

    const contactForm = document.getElementById('form-contact');

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        hideError()

        const name = document.getElementById('text-name').value.trim();
        if (!name) {
            showError("El nombre es obligatorio");
            document.getElementById('text-name').focus()
            return;
        }

        const subject = document.getElementById('text-subject').value.trim();
        if (!subject) {
            showError("El asunto es obligatorio");
            document.getElementById('text-subject').focus()
            return;
        }

        const email = document.getElementById('email').value.trim();
        if (!email) {
            showError("El email es obligatorio");
            document.getElementById('email').focus()
            return;
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showError("El email tiene un formato incorrecto");
            document.getElementById('email').focus()
            return;
        }

        const message = document.getElementById('txa-message').value.trim();
        if (!message) {
            showError("El mensaje es obligatorio");
            document.getElementById('txa-message').focus()
            return;
        }

        document.getElementById("hid-token").value = "<?= $token ?>";
        document.getElementById("button-submit").disabled = true;

        contactForm.submit()

    });

    // Lazy loading images with Intersection Observer
    if ('IntersectionObserver' in window) {
        const imgOptions = {
            threshold: 0.1,
            rootMargin: "0px 0px 200px 0px"
        };

        const imgObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.getAttribute('data-src');
                    if (src) {
                        img.src = src;
                        img.removeAttribute('data-src');
                    }
                    observer.unobserve(img);
                }
            });
        }, imgOptions);

        document.querySelectorAll('img[data-src]').forEach(img => {
            imgObserver.observe(img);
        });
    } else {
        // Fallback for browsers that don't support Intersection Observer
        document.querySelectorAll('img[data-src]').forEach(img => {
            img.src = img.getAttribute('data-src');
            img.removeAttribute('data-src');
        });
    }

    // Add animation on scroll
    if ('IntersectionObserver' in window) {
        const animateOptions = {
            threshold: 0.1,
            rootMargin: "0px 0px -100px 0px"
        };

        const animateObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.remove("opacity-0");
                    entry.target.classList.add("opacity-1");
                    animateObserver.unobserve(entry.target);
                }
            });
        }, animateOptions);

        document.querySelectorAll(".bg-gray-100").forEach(el => {
            el.classList.add("opacity-0", "transition-opacity", "duration-1000");
            animateObserver.observe(el);
        });
    }
});
</script>
</body>
</html>