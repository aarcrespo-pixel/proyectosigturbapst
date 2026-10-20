const perfil = document.querySelector(".perfil"); // caja del perfil
const botonPerfil = document.querySelector(".perfil-btn"); // boton del perfil
const perfilMenu = document.querySelector(".perfil-menu"); // menu desplegable del perfil
const hamburguesa = document.querySelector(".hamburguesa"); // boton menu movil
const menu = document.querySelector(".menu"); // barra principal del menu
const menuPrincipal = document.querySelector(".menu-principal");
const body = document.body; // cuerpo del html

const isIndex = !document.querySelector('main'); // la portada es la única vista sin un elemento main
const isEventos = !!document.querySelector("main.pagina-eventos"); // pagina de eventos
const isTurismo = !!document.querySelector("main.pagina-turismo"); // pagina de turismo
const isLugares = !!document.querySelector("main.pagina-lugares"); // pagina de lugares

const infoButton = document.querySelector('.info-btn'); // boton info del hero del index
const infoPanel = document.querySelector('.info-panel'); // panel info en el index
const footerInfoButton = document.querySelector('.footer-info-button'); // boton info del footer
const bottomNavUsuario = document.querySelector('.bottom-nav-item[href$="login.html"]'); // boton usuario en menu inferior


/* Esta utilidad concentra las escrituras de estilo para que el cambio de tema
    pueda reutilizar la misma operación en body, textos y controles. */
const setStyle = (elemento, propiedad, valor) => {
    if (elemento) {
        elemento.style[propiedad] = valor; // cambia estilo directo con .style
    }
};

const storageClaveTema = 'modo-oscuro'; // guardamos si estamos en dark mode o no
const storageClaveIdioma = 'sigtur-idioma';
const storageClaveTamanoLetra = 'sigtur-tamano-letra';
const escalasTamanoLetra = {
    pequeno: 0.9,
    normal: 1,
    grande: 1.15,
    muyGrande: 1.3
};

const aplicarTamanoLetra = (tamano = 'normal') => {
    const tamanoValido = escalasTamanoLetra[tamano] ? tamano : 'normal';
    document.documentElement.style.fontSize = `${escalasTamanoLetra[tamanoValido] * 100}%`;
    document.documentElement.dataset.tamanoLetra = tamanoValido;
};

const guardarTamanoLetra = (tamano) => {
    const tamanoValido = escalasTamanoLetra[tamano] ? tamano : 'normal';
    localStorage.setItem(storageClaveTamanoLetra, tamanoValido);
    aplicarTamanoLetra(tamanoValido);
};

const leerTamanoLetra = () => localStorage.getItem(storageClaveTamanoLetra) || 'normal';

aplicarTamanoLetra(leerTamanoLetra());
const idiomasDisponibles = ['es', 'en', 'pt'];

const traducciones = {
    es: {
        navHome: 'Inicio',
        navEvents: 'Eventos',
        navTourism: 'Turismo',
        navPlaces: 'Lugares',
        navGastronomy: 'Gastronomía',
        navLogin: 'Ingresar Usuario',
        navSettings: 'Configuración',
        navSupport: 'Soporte',
        navLogout: 'Cerrar Sesión',
        searchPlaceholder: 'Buscar eventos',
        homeScroll: 'Desliza para ver más',
        homeInfoButton: 'Información',
        homeInfoTitle: '¿Qué es SIGTUR?',
        homeInfoText1: 'SIGTUR es una guía local de Salto pensada para ayudarte a descubrir eventos, lugares y experiencias únicas de la ciudad.',
        homeInfoText2: 'La plataforma reúne recomendaciones culturales, turísticas y de ocio para que cada visita sea más simple, informada y memorable.',
        homeFeaturesTitle: 'Eventos Destacados',
        homeFeatureEvents: 'Eventos',
        homeFeatureTourism: 'Turismo',
        homeFeaturePlaces: 'Lugares',
        homeFeatureEventsText1: 'Descubre las fechas y actividades destacadas para planificar tu visita a Salto.',
        homeFeatureEventsText2: 'Explora propuestas para cada estilo y no te pierdas lo mejor que ofrece la ciudad.',
        homeFeatureTourismText1: 'Encuentra ideas de recorridos y propuestas para disfrutar la ciudad y sus alrededores.',
        homeFeatureTourismText2: 'Conecta con opciones de paseos, experiencias y consejos para tu viaje.',
        homeFeaturePlacesText1: 'Conoce los puntos más emblemáticos y los sitios imperdibles de Salto.',
        homeFeaturePlacesText2: 'Descubre lugares únicos donde vivir momentos especiales y recordar la visita.',
        homeFeatureGastronomy: 'Gastronomía',
        homeFeatureGastronomyText1: 'Descubre sabores locales, cafeterías y propuestas para disfrutar cada momento.',
        homeFeatureGastronomyText2: 'Encuentra opciones para compartir, conocer la identidad de Salto y volver por más.',
        homeEventsTitle: 'Eventos Destacados',
        homeEventButton: 'Ver más',
        homePlacesTitle: 'Lugares destacados',
        homeGastronomyTitle: 'Gastronomía destacada',
        homePlacesButton: 'Ver lugares',
        homeGastronomyButton: 'Ver gastronomía',
        homePlacesPrev: 'Lugar anterior',
        homePlacesNext: 'Siguiente lugar',
        homeGastronomyPrev: 'Lugar gastronómico anterior',
        homeGastronomyNext: 'Siguiente lugar gastronómico',
        homePlaceArtigasText: 'Un paseo céntrico para disfrutar de historia, arquitectura y movimiento local.',
        homePlaceWaterfrontText: 'Un recorrido junto al río para caminar, descansar y disfrutar el atardecer.',
        homePlaceSolariText: 'Espacios verdes y aire libre para compartir una salida tranquila.',
        homeGastronomyTrouvilleText: 'Una propuesta cálida para disfrutar café, algo rico y una buena charla en el centro.',
        homeGastronomyShoppingText: 'Un punto de encuentro para pasear, hacer una pausa y elegir entre distintas propuestas.',
        homeGastronomyCuisineText: 'Propuestas de cocina casera, minutas y platos locales para un almuerzo completo.',
        footerEvents: 'EVENTOS',
        footerPlaces: 'LUGARES',
        footerTourism: 'TURISMO',
        footerGastronomy: 'GASTRONOMÍA',
        footerSigtur: 'SIGTUR',
        footerEventLinks: ['Eventos destacados', 'Próximos eventos', 'Eventos anteriores', 'Todos los eventos'],
        footerPlaceLinks: ['Plaza Artigas', 'Plaza Treinta y Tres Orientales', 'Costanera Norte', 'Costanera Sur', 'Parque Benito Solari'],
        footerGastronomyLinks: ['Restaurantes', 'Cafés y propuestas locales', 'Explorar gastronomía'],
        footerTourismLinks: ['Actividades', 'Lugares turísticos', 'Experiencias'],
        footerLegal: 'SIGTUR © 2026 by Bapst is licensed under CC BY-NC-ND 4.0',
        configBack: '← Volver',
        configEyebrow: '⚙️ Configuración',
        configTitle: 'Personaliza tu experiencia en la página',
        configDescription: 'Aquí tienes la seccion en donde podras configurar las preferencias de usuario y las opciones de apariencia a tu gusto.',
        configGeneralTitle: 'Datos generales',
        configGeneralText: 'Define la identidad y la información principal de la página.',
        configLabelSiteName: 'Nombre del sitio',
        configLabelEmail: 'Correo de contacto',
        configLabelCity: 'Ciudad',
        configVisualTitle: 'Preferencias de visualización',
        configVisualText: 'Controla la forma en que se presenta la información al usuario.',
        configLabelTheme: 'Tema',
        configLabelLanguage: 'Idioma',
        configLabelDescription: 'Descripción breve',
        configNotificationsTitle: 'Notificaciones',
        configNotificationsText: 'Activa o desactiva los avisos importantes del proyecto.',
        configNotificationEventsTitle: 'Eventos destacados',
        configNotificationEventsText: 'Recibe alertas de actividades nuevas o próximas.',
        configNotificationRemindersTitle: 'Recordatorios',
        configNotificationRemindersText: 'Envío de avisos de actualización o mantenimiento.',
        configSecurityTitle: 'Seguridad y acceso',
        configSecurityText: 'Gestiona los accesos y el control de la plataforma.',
        configAccessPublicTitle: 'Acceso público',
        configAccessPublicText: 'Permite ver el contenido sin iniciar sesión.',
        configAdminPanelTitle: 'Panel de administración',
        configAdminPanelText: 'Acceso privado para editar contenido y configuraciones.',
        configSave: 'Guardar cambios',
        configReset: 'Restablecer',
        themeDark: 'Oscuro',
        themeLight: 'Claro',
        languageOptionEs: 'Español',
        languageOptionEn: 'English',
        languageOptionPt: 'Português',
        languageOptionFr: 'Français',
        languageOptionIt: 'Italiano',
        languageOptionDe: 'Deutsch',
        languageOptionJa: '日本語',
        languageOptionZh: '中文',
        languageOptionAr: 'العربية',
        languageOptionRu: 'Русский',
        eventsHeroLabel: 'EVENTOS',
        eventsHeroTitle: 'EVENTOS',
        eventsHeroText: 'Este apartado reúne los diferentes eventos turísticos, deportivos y culturales de Salto para que puedas informarte y participar en actividades divertidas.',
        eventsFeaturedLabel: 'Destacados',
        tourismHeroLabel: 'Descubrí el turismo en Salto',
        tourismHeroTitle: 'TURISMO',
        tourismHeroText: 'Explorá propuestas de turismo local, recorridos gastronómicos y experiencias al aire libre diseñadas para cada tipo de visitante.',
        placesHeroLabel: 'Lugares históricos',
        placesHeroTitle: 'LUGARES',
        placesHeroText: 'Descubre los lugares más emblemáticos de Salto, llenos de historia y cultura.'
    },
    en: {
        navHome: 'Home',
        navEvents: 'Events',
        navTourism: 'Tourism',
        navPlaces: 'Places',
        navGastronomy: 'Cuisine',
        navLogin: 'Log in',
        navSettings: 'Settings',
        navSupport: 'Support',
        navLogout: 'Log out',
        searchPlaceholder: 'Search events',
        homeScroll: 'Swipe to see more',
        homeInfoButton: 'Info',
        homeInfoTitle: 'What is SIGTUR?',
        homeInfoText1: 'SIGTUR is a local guide for Salto designed to help you discover the city’s unique events, places and experiences.',
        homeInfoText2: 'The platform brings together cultural, tourism and leisure recommendations so each visit is simpler, better informed and more memorable.',
        homeFeaturesTitle: 'Featured Events',
        homeFeatureEvents: 'Events',
        homeFeatureTourism: 'Tourism',
        homeFeaturePlaces: 'Places',
        homeFeatureEventsText1: 'Discover the standout dates and activities to plan your visit to Salto.',
        homeFeatureEventsText2: 'Explore ideas for every style and don’t miss what the city has to offer.',
        homeFeatureTourismText1: 'Find route ideas and proposals to enjoy the city and its surroundings.',
        homeFeatureTourismText2: 'Connect with walking experiences, activities and tips for your trip.',
        homeFeaturePlacesText1: 'Discover the city’s most emblematic points and must-see spots in Salto.',
        homeFeaturePlacesText2: 'Find unique places where you can create special memories.',
        homeFeatureGastronomy: 'Cuisine',
        homeFeatureGastronomyText1: 'Discover local flavours, cafés and proposals to enjoy every moment.',
        homeFeatureGastronomyText2: 'Find options to share, learn about Salto’s identity and come back for more.',
        homeEventsTitle: 'Featured Events',
        homeEventButton: 'See more',
        homePlacesTitle: 'Featured Places',
        homeGastronomyTitle: 'Featured Cuisine',
        homePlacesButton: 'View places',
        homeGastronomyButton: 'View cuisine',
        homePlacesPrev: 'Previous place',
        homePlacesNext: 'Next place',
        homeGastronomyPrev: 'Previous cuisine spot',
        homeGastronomyNext: 'Next cuisine spot',
        homePlaceArtigasText: 'A central promenade to enjoy history, architecture and local life.',
        homePlaceWaterfrontText: 'A riverside route for walking, relaxing and enjoying the sunset.',
        homePlaceSolariText: 'Green spaces and fresh air for a peaceful outing.',
        homeGastronomyTrouvilleText: 'A welcoming spot to enjoy coffee, a treat and a good conversation downtown.',
        homeGastronomyShoppingText: 'A meeting point to stroll, take a break and choose from different options.',
        homeGastronomyCuisineText: 'Home-style cooking, quick meals and local dishes for a complete lunch.',
        footerEvents: 'EVENTS',
        footerPlaces: 'PLACES',
        footerTourism: 'TOURISM',
        footerGastronomy: 'GASTRONOMY',
        footerSigtur: 'SIGTUR',
        footerEventLinks: ['Featured events', 'Upcoming events', 'Past events', 'All events'],
        footerPlaceLinks: ['Plaza Artigas', 'Plaza Treinta y Tres Orientales', 'North waterfront', 'South waterfront', 'Parque Benito Solari'],
        footerGastronomyLinks: ['Restaurants', 'Cafés and local spots', 'Explore gastronomy'],
        footerTourismLinks: ['Activities', 'Tourist spots', 'Experiences'],
        footerLegal: 'SIGTUR © 2026 by Bapst is licensed under CC BY-NC-ND 4.0',
        configBack: '← Back',
        configEyebrow: '⚙️ Settings',
        configTitle: 'Personalize your experience on the page',
        configDescription: 'Here is the section where you can configure user preferences and appearance options to your liking.',
        configGeneralTitle: 'General information',
        configGeneralText: 'Define the identity and main information of the page.',
        configLabelSiteName: 'Site name',
        configLabelEmail: 'Contact email',
        configLabelCity: 'City',
        configVisualTitle: 'Display preferences',
        configVisualText: 'Control how information is presented to the user.',
        configLabelTheme: 'Theme',
        configLabelLanguage: 'Language',
        configLabelDescription: 'Short description',
        configNotificationsTitle: 'Notifications',
        configNotificationsText: 'Enable or disable important project alerts.',
        configNotificationEventsTitle: 'Featured events',
        configNotificationEventsText: 'Receive alerts for new or upcoming activities.',
        configNotificationRemindersTitle: 'Reminders',
        configNotificationRemindersText: 'Send update or maintenance notices.',
        configSecurityTitle: 'Security and access',
        configSecurityText: 'Manage platform access and control.',
        configAccessPublicTitle: 'Public access',
        configAccessPublicText: 'Allow viewing content without logging in.',
        configAdminPanelTitle: 'Administration panel',
        configAdminPanelText: 'Private access to edit content and settings.',
        configSave: 'Save changes',
        configReset: 'Reset',
        themeDark: 'Dark',
        themeLight: 'Light',
        languageOptionEs: 'Español',
        languageOptionEn: 'English',
        languageOptionPt: 'Português',
        languageOptionFr: 'Français',
        languageOptionIt: 'Italiano',
        languageOptionDe: 'Deutsch',
        languageOptionJa: '日本語',
        languageOptionZh: '中文',
        languageOptionAr: 'العربية',
        languageOptionRu: 'Русский',
        eventsHeroLabel: 'EVENTS',
        eventsHeroTitle: 'EVENTS',
        eventsHeroText: 'This section brings together the different tourist, sports and cultural events of Salto so you can stay informed and take part in fun activities.',
        eventsFeaturedLabel: 'Featured',
        tourismHeroLabel: 'Discover tourism in Salto',
        tourismHeroTitle: 'TOURISM',
        tourismHeroText: 'Explore local tourism proposals, gastronomic routes and outdoor experiences designed for every type of visitor.',
        placesHeroLabel: 'Historic places',
        placesHeroTitle: 'PLACES',
        placesHeroText: 'Discover the most emblematic places in Salto, full of history and culture.'
    },
    pt: {
        navHome: 'Início',
        navEvents: 'Eventos',
        navTourism: 'Turismo',
        navPlaces: 'Locais',
        navGastronomy: 'Gastronomia',
        navLogin: 'Entrar',
        navSettings: 'Configurações',
        navSupport: 'Suporte',
        navLogout: 'Sair',
        searchPlaceholder: 'Buscar eventos',
        homeScroll: 'Deslize para ver mais',
        homeInfoButton: 'Informação',
        homeInfoTitle: 'O que é o SIGTUR?',
        homeInfoText1: 'O SIGTUR é um guia local de Salto pensado para ajudar a descobrir eventos, lugares e experiências únicas da cidade.',
        homeInfoText2: 'A plataforma reúne recomendações culturais, turísticas e de lazer para que cada visita seja mais simples, informada e memorável.',
        homeFeaturesTitle: 'Eventos em destaque',
        homeFeatureEvents: 'Eventos',
        homeFeatureTourism: 'Turismo',
        homeFeaturePlaces: 'Locais',
        homeFeatureEventsText1: 'Descubra as datas e atividades destacadas para planejar sua visita a Salto.',
        homeFeatureEventsText2: 'Explore propostas para cada estilo e não perca o melhor que a cidade oferece.',
        homeFeatureTourismText1: 'Encontre ideias de percursos e propostas para aproveitar a cidade e arredores.',
        homeFeatureTourismText2: 'Conecte-se a passeios, experiências e conselhos para sua viagem.',
        homeFeaturePlacesText1: 'Conheça os pontos mais emblemáticos e os lugares imperdíveis de Salto.',
        homeFeaturePlacesText2: 'Descubra lugares únicos onde viver momentos especiais e lembrar da visita.',
        homeFeatureGastronomy: 'Gastronomia',
        homeFeatureGastronomyText1: 'Descubra sabores locais, cafeterias e propostas para aproveitar cada momento.',
        homeFeatureGastronomyText2: 'Encontre opções para compartilhar, conhecer a identidade de Salto e voltar sempre.',
        homeEventsTitle: 'Eventos em destaque',
        homeEventButton: 'Ver mais',
        homePlacesTitle: 'Lugares em destaque',
        homeGastronomyTitle: 'Gastronomia em destaque',
        homePlacesButton: 'Ver lugares',
        homeGastronomyButton: 'Ver gastronomia',
        homePlacesPrev: 'Lugar anterior',
        homePlacesNext: 'Próximo lugar',
        homeGastronomyPrev: 'Lugar gastronômico anterior',
        homeGastronomyNext: 'Próximo lugar gastronômico',
        homePlaceArtigasText: 'Um passeio central para aproveitar a história, a arquitetura e a vida local.',
        homePlaceWaterfrontText: 'Um percurso junto ao rio para caminhar, descansar e apreciar o pôr do sol.',
        homePlaceSolariText: 'Áreas verdes e ar livre para um passeio tranquilo.',
        homeGastronomyTrouvilleText: 'Um lugar acolhedor para tomar café, comer algo gostoso e conversar no centro.',
        homeGastronomyShoppingText: 'Um ponto de encontro para passear, fazer uma pausa e escolher entre várias opções.',
        homeGastronomyCuisineText: 'Comida caseira, pratos rápidos e receitas locais para um almoço completo.',
        footerEvents: 'EVENTOS',
        footerPlaces: 'LOCAIS',
        footerTourism: 'TURISMO',
        footerGastronomy: 'GASTRONOMIA',
        footerSigtur: 'SIGTUR',
        footerEventLinks: ['Eventos em destaque', 'Próximos eventos', 'Eventos anteriores', 'Todos os eventos'],
        footerPlaceLinks: ['Praça Artigas', 'Praça Treinta y Tres Orientales', 'Costanera Norte', 'Costanera Sul', 'Parque Benito Solari'],
        footerGastronomyLinks: ['Restaurantes', 'Cafés e propostas locais', 'Explorar gastronomia'],
        footerTourismLinks: ['Atividades', 'Locais turísticos', 'Experiências'],
        footerLegal: 'SIGTUR © 2026 por Bapst está licenciado sob CC BY-NC-ND 4.0',
        configBack: '← Voltar',
        configEyebrow: '⚙️ Configurações',
        configTitle: 'Personalize sua experiência na página',
        configDescription: 'Aqui está a seção onde você poderá configurar as preferências de usuário e as opções de aparência ao seu gosto.',
        configGeneralTitle: 'Dados gerais',
        configGeneralText: 'Defina a identidade e as informações principais da página.',
        configLabelSiteName: 'Nome do site',
        configLabelEmail: 'E-mail de contato',
        configLabelCity: 'Cidade',
        configVisualTitle: 'Preferências de visualização',
        configVisualText: 'Controle a forma como as informações são apresentadas ao usuário.',
        configLabelTheme: 'Tema',
        configLabelLanguage: 'Idioma',
        configLabelDescription: 'Descrição curta',
        configNotificationsTitle: 'Notificações',
        configNotificationsText: 'Ative ou desative os avisos importantes do projeto.',
        configNotificationEventsTitle: 'Eventos em destaque',
        configNotificationEventsText: 'Receba alertas de atividades novas ou próximas.',
        configNotificationRemindersTitle: 'Lembretes',
        configNotificationRemindersText: 'Envio de avisos de atualização ou manutenção.',
        configSecurityTitle: 'Segurança e acesso',
        configSecurityText: 'Gerencie os acessos e o controle da plataforma.',
        configAccessPublicTitle: 'Acesso público',
        configAccessPublicText: 'Permite ver o conteúdo sem fazer login.',
        configAdminPanelTitle: 'Painel de administração',
        configAdminPanelText: 'Acesso privado para editar conteúdo e configurações.',
        configSave: 'Salvar alterações',
        configReset: 'Restaurar',
        themeDark: 'Escuro',
        themeLight: 'Claro',
        languageOptionEs: 'Español',
        languageOptionEn: 'English',
        languageOptionPt: 'Português',
        languageOptionFr: 'Français',
        languageOptionIt: 'Italiano',
        languageOptionDe: 'Deutsch',
        languageOptionJa: '日本語',
        languageOptionZh: '中文',
        languageOptionAr: 'العربية',
        languageOptionRu: 'Русский',
        eventsHeroLabel: 'EVENTOS',
        eventsHeroTitle: 'EVENTOS',
        eventsHeroText: 'Esta seção reúne os diferentes eventos turísticos, esportivos e culturais de Salto para que você possa se informar e participar de atividades divertidas.',
        eventsFeaturedLabel: 'Destaques',
        tourismHeroLabel: 'Descubra o turismo em Salto',
        tourismHeroTitle: 'TURISMO',
        tourismHeroText: 'Explore propostas de turismo local, rotas gastronômicas e experiências ao ar livre concebidas para cada tipo de visitante.',
        placesHeroLabel: 'Lugares históricos',
        placesHeroTitle: 'LUGARES',
        placesHeroText: 'Descubra os lugares mais emblemáticos de Salto, cheios de história e cultura.'
    }
};

const traduccionesExtra = {
    es: {
        navProfile: 'Perfil',
        navMenuOpen: 'Abrir menú',
        navSearch: 'Buscar',
        searchResultsTitle: 'Resultados para:',
        placeAboutKicker: 'Descubrí Salto',
        placeAboutTitle: 'Sobre este lugar',
        placeEventsKicker: 'Agenda local',
        placeEventsTitle: 'Próximos eventos en este lugar',
        placeHistoryTitle: 'Historia y patrimonio',
        placeHoursLabel: 'Horarios',
        placeMapButton: 'Abrir ubicación en Maps',
        placeNoEvents: 'No hay eventos próximos en este lugar.',
        placeRecommendationsLabel: 'Recomendaciones',
        placeThingsTitle: 'Qué hacer y atractivos',
        placeUsefulTitle: 'Información útil y ubicación',
        placeViewMore: 'Ver más',
        pageHomeTitle: 'Inicio | SIGTUR',
        pageEventsTitle: 'Eventos | SIGTUR',
        pageTourismTitle: 'Turismo | SIGTUR',
        pagePlacesTitle: 'Lugares | SIGTUR',
        pageConfigTitle: 'Configuración | SIGTUR',
        pageLoginTitle: 'Iniciar sesión | SIGTUR',
        pageRegisterTitle: 'Registrarse | SIGTUR',
        pageSupportTitle: 'Soporte | SIGTUR',
        pageTodosEventsTitle: 'Todos los eventos | SIGTUR',
        loginTitle: 'Iniciar Sesión',
        loginIntro: 'Completa tus datos y registra tu cuenta o inicia sesión.',
        loginUsername: 'Usuario / Correo Electrónico',
        loginUsernamePlaceholder: 'Juan Perez o juanperez@gmail.com',
        loginPassword: 'Contraseña',
        loginPasswordPlaceholder: '1234567',
        loginForgot: 'Olvidé mi contraseña',
        loginRegister: 'Registrarse',
        loginSubmit: 'Iniciar Sesión',
        loginGoogle: 'Iniciar con Google',
        loginGoogleAlt: 'Iniciar con Google',
        loginLegal: 'Al iniciar sesión aceptas los términos y condiciones de la empresa, las reglas de uso y la política de privacidad.',
        registerTitle: 'Regístrate',
        registerIntro: 'Completa tus datos para crear tu cuenta y empezar a disfrutar del sitio.',
        registerName: 'Nombre completo',
        registerNamePlaceholder: 'Juan Perez',
        registerEmail: 'Correo electrónico',
        registerEmailPlaceholder: 'juanperez@gmail.com',
        registerPassword: 'Contraseña',
        registerPasswordPlaceholder: 'Nombre10394.',
        registerPhone: 'Número telefónico',
        registerPhonePlaceholder: '+598 99 123 456',
        registerSubmit: 'Registrarse',
        registerLogin: 'Iniciar Sesión',
        registerGoogle: 'Iniciar con Google',
        registerGoogleAlt: 'Iniciar con Google',
        registerLegal: 'Al registrarte aceptas los términos y condiciones de la empresa, las reglas de uso y la política de privacidad.',
        supportBack: '← Volver',
        loginBack: '← Volver al inicio',
        loginBackAlt: 'Volver al inicio',
        supportEyebrow: 'Centro de soporte',
        supportTitle: 'Estamos para ayudarte',
        supportLead: 'Encuentra respuestas rápidas, envía un reporte o contacta al equipo de soporte para resolver cualquier duda sobre el proyecto.',
        supportFaqTitle: 'Preguntas frecuentes',
        supportFaqText: 'Respuestas rápidas a las dudas más comunes.',
        supportReportTitle: 'Reportes',
        supportReportText: 'Describe el problema para que el equipo lo revise.',
        supportContactTitle: 'Contacto',
        supportContactText: 'Opciones para comunicarte con soporte.',
        supportHelpTitle: 'Ayuda rápida',
        supportHelpText: 'Guías breves para resolver tareas simples.',
        supportFaq1Question: '¿Cómo accedo al panel?',
        supportFaq1Answer: 'Inicia sesión con tus credenciales de usuario.',
        supportFaq2Question: '¿Puedo cambiar la configuración?',
        supportFaq2Answer: 'Sí, desde la sección de configuración del sitio.',
        supportFaq3Question: '¿Cómo reporto un error?',
        supportFaq3Answer: 'Usa el formulario de reportes en esta misma página.',
        supportReportSubject: 'Asunto del reporte',
        supportReportEmail: 'Tu correo',
        supportReportTextarea: 'Describe el problema o la incidencia...',
        supportReportButton: 'Enviar reporte',
        supportContactEmail: 'Correo: soporte@sigtur.com',
        supportContactWhatsapp: 'WhatsApp: 091 661 335',
        supportContactInstagram: 'Instagram: @bapstuy',
        supportContactHours: 'Horario: Lunes a Viernes, 9:00 a 18:00',
        supportHelp1Title: 'Actualizar datos',
        supportHelp1Text: 'Ve a configuración y guarda los cambios.',
        supportHelp2Title: 'Revisar eventos',
        supportHelp2Text: 'Consulta la sección de eventos y filtros disponibles.',
        supportHelp3Title: 'Solucionar acceso',
        supportHelp3Text: 'Verifica tu usuario, contraseña y conexión.',
        todosEventsBack: '← Volver al inicio',
        todosEventsSearch: 'Buscar eventos',
        todosEventsCategoryLabel: 'Todas',
        todosEventsFilterRecent: 'Más recientes',
        todosEventsFilterRecommended: 'Recomendados',
        todosEventsFilterFree: 'Gratis',
        todosEventsHeroLabel: 'Explorá lo que pasa',
        todosEventsHeroTitle: 'Todos los eventos',
        todosEventsHeroText: 'Descubrí actividades culturales, deportivas y sociales en un solo lugar.',
        todosEventsCategoriesTitle: 'Carreras',
        todosEventsCategoriesCultural: 'Culturales',
        todosEventsCategoriesSport: 'Deportivos',
        todosEventsNavPrev: 'Eventos anteriores',
        todosEventsNavNext: 'Eventos siguientes',
        todosEventsCardCategory: ['Cultural', 'Deportivo', 'Social'],
        todosEventsCardTitles: ['Festival de la Música', 'Carrera de la Costa', 'Noche de Arte', 'Tarde en el Parque', 'Muestra de Astronomía', 'Torneo de Fútbol', 'Stand Up Nocturno', 'Expo Gastronómica', 'Festival de la Naranja', 'Taller de Mural', 'Cine al Aire Libre', 'Teatro Comunal', 'Maratón del Sol', 'Triatlón Familiar', 'Ciclismo Urbano', 'Clase de Yoga', 'Copa de Mountain Bike', 'Marcha de la Salud'],
        todosEventsCardDates: ['12 agosto 2026', '19 agosto 2026', '24 agosto 2026', '2 septiembre 2026', '5 octubre 2026', '8 octubre 2026', '12 octubre 2026', '18 septiembre 2026', '30 septiembre 2026', '1 octubre 2026', '3 octubre 2026', '6 octubre 2026', '10 septiembre 2026', '16 septiembre 2026', '28 septiembre 2026', '9 octubre 2026', '14 octubre 2026', '20 octubre 2026'],
        todosEventsCardCategories: ['Cultural', 'Deportivo', 'Cultural', 'Social', 'Cultural', 'Deportivo', 'Social', 'Social', 'Cultural', 'Cultural', 'Cultural', 'Cultural', 'Deportivo', 'Deportivo', 'Deportivo', 'Deportivo', 'Deportivo', 'Deportivo'],
        eventsMainCategories: ['Deportivos', 'Discotecas', 'Competencias'],
        eventsPreviousTitle: 'Eventos Anteriores',
        eventsReviewsTitle: 'Galería',
        eventsGalleryTitle: 'Galería',
        eventsViewAll: 'Ver todos los eventos',
        eventsViewPrevious: 'Ver eventos anteriores',
        eventsReviewNames: ['Benjamín R.', 'Aaron C.', 'Santiago D.', 'Federico S.', 'Pío M.'],
        tourismSectionEscapadasTitle: 'Escapadas recomendadas',
        tourismSectionRoutesTitle: 'Rutas y experiencias',
        tourismDiscoverMore: 'Descubrir más',
        tourismHotelsTitle: 'Hoteles',
        tourismHotelsSeeAll: 'Ver todos los hoteles',
        tourismWaterparksTitle: 'Parques Acuáticos y Termas',
        tourismEscapadasTitles: ['Costanera Norte', 'BaSalto', 'Parque Benito Solari'],
        tourismEscapadasDescriptions: [
            'Un paseo al borde del río para disfrutar de arte urbano, música en vivo y atardeceres junto al agua.',
            'Centro histórico con monumentos, cafés y espacios culturales que invitan a recorrer la identidad local.',
            'Un lugar verde donde se mezclan senderos, picnic y actividades deportivas al aire libre.'
        ],
        tourismEscapadasMeta: ['Ideal para familias · 3 km', 'Cultura · Centro', 'Naturaleza · Relax'],
        tourismEscapadasAlt: ['Costanera de Salto', 'Basalto', 'Parque Solari'],
        tourismRoutesTitles: ['La Trouville', 'Cine Sarandi', 'La Fosa', 'Salto Shopping'],
        tourismRoutesDescriptions: [
            'Probá platos típicos y descubrí sabores locales en mercados, parrillas y cafeterías con encanto.',
            'Itinerarios para vivir la ciudad de noche con espectáculos, bares y rincones de música en vivo.',
            'Circuito de skate, bicicleta y barras calistenicas.',
            'Shopping con variedad de tiendas y zonas para comer.'
        ],
        tourismRoutesMeta: ['Comida', 'Cine', 'Deportes · Guía disponible', 'Shopping'],
        tourismRoutesAlt: ['Trouville', 'Cine Sarandi', 'La Fosa', 'Shopping Salto'],
        tourismHotelNames: ['Salto Hotel & Casino', 'Hotel Eldorado', 'Hotel Español', 'Hotel Horacio Quiroga'],
        tourismHotelPrices: ['$230 por noche', '$310 por noche', '$185 por noche', '$270 por noche'],
        tourismHotelLocations: ['Dirección: 25 de Agosto 05', 'Dirección: Av. Sarandi 20', 'Dirección: Barrio Hipódromo', 'Dirección: Brasil 826'],
        tourismHotelStars4: '4 estrellas',
        tourismHotelStars5: '5 estrellas',
        tourismHotelAlt: ['Salto Hotel & Casino', 'Hotel Eldorado', 'Hotel Español', 'Hotel Horacio Quiroga'],
        tourismWaterparkTitles: ['Termas del Dayman', 'Acuamania', 'Termas de la Arapey', 'Agua Clara'],
        tourismWaterparkDescriptions: [
            'Termas naturales con aguas termales.',
            'Parque acuático con diversas atracciones y áreas de descanso.',
            'Termas para relajarse en familia.',
            'Piscinas relajantes para disfrutar en las vacaciones.'
        ],
        tourismWaterparkMeta: ['Termas', 'Parque Acuático', 'Termas', 'Termas'],
        tourismWaterparkAlt: ['Dayman', 'Aquamania', 'La Arapey', 'Aguas Claras'],
        tourismGastronomyTitle: 'Gastronomía',
        tourismGastronomySeeAll: 'Ver toda la gastronomía salteña',
        tourismGalleryTitle: 'Galería turística',
        tourismGallerySeeAll: 'Ver todas las galerías',
        tourismGalleryIntro: 'Paisajes, paseos y lugares para inspirar tu próxima visita.',
        tourismGalleryTitles: ['Parque Benito Solari', 'Termas del Dayman', 'Costanera Norte', 'Termas de Arapey', 'Plaza Artigas', 'Shopping Salto', 'Cine Sarandí', 'Paisajes del litoral'],
        tourismGalleryAlt: ['Parque Solari', 'Piscinas de las Termas del Dayman', 'Paseo por la Costanera Norte', 'Complejo termal de Salto', 'Monumentos de la Plaza Artigas', 'Shopping Salto', 'Calle céntrica junto al Cine Sarandí', 'Paisaje natural de la región'],
        placesSectionEmblematicTitle: 'Lugares emblemáticos',
        placesSectionCategoriesTitle: 'Categorías de lugares',
        placesSectionLeisureTitle: 'Lugares para Distraerse',
        placesSectionGalleryTitle: 'Galería Histórica',
        placesMoreButton: 'Ver más lugares',
        placesGalleryButton: 'Ver todas las galerías',
        placesEmblematicTitles: ['Plaza Treinta y Tres Orientales', 'Plaza Artigas', 'Parque Benito Solari'],
        placesEmblematicDescriptions: [
            'Paseo tranquilo junto al río con espacios verdes, miradores y zonas para descansar al aire libre.',
            'Plaza emblemática del centro con arquitectura tradicional, cafeterías y actividad cultural permanente.',
            'Gran área verde perfecta para caminatas familiares, actividades deportivas y eventos al aire libre.'
        ],
        placesEmblematicAlt: ['Costanera Sur', 'Plaza histórica', 'Parque Benito Solari'],
        placesCategoryTitles: ['Gastronomía', 'Cultura', 'Naturaleza', 'Experiencias'],
        placesCategoryDescriptions: [
            'Selección de cafés, parrillas y locales con sabores locales que marcan tendencia en la ciudad.',
            'Galerías, murales y espacios históricos que muestran la identidad y patrimonio de Salto.',
            'Áreas verdes, parques y paseos naturales para los amantes del aire libre y la calma.',
            'Actividades interactivas, mercados y puntos de encuentro que enriquecen cada visita.'
        ],
        placesCategoryAlt: ['Cafés y gastronomía', 'Arte urbano', 'Espacios naturales', 'Atracciones urbanas'],
        placesLeisureTitles: ['Plaza Roosvelt', 'Costanera Norte', 'Muelle Negro'],
        placesLeisureDescriptions: [
            'Plaza tranquila en la costa para tomar mates con amigos u familiares.',
            'Área costera con vistas al río, espacios verdes y zonas para actividades al aire libre.',
            'Zona con vista al río para relajarse y pasar el rato con amigos.'
        ],
        placesLeisureAlt: ['Plaza Roosvelt', 'Costanera Norte', 'Muelle Negro'],
        placesGalleryIntro: 'Un recorrido visual por los espacios y las tradiciones que cuentan la historia de Salto.',
        placesGalleryTitles: ['Plaza Treinta y Tres', 'Plaza Artigas', 'Centro histórico', 'Cultura salteña', 'Costanera de Salto'],
        placesGalleryAlt: ['Plaza Treinta y Tres y su entorno histórico', 'Monumentos de la Plaza Artigas', 'Calle céntrica junto al Cine Sarandí', 'Expresión de la cultura popular salteña', 'Paseo costero de Salto']
    },
    en: {
        navProfile: 'Profile',
        navMenuOpen: 'Open menu',
        navSearch: 'Search',
        searchResultsTitle: 'Search results for:',
        placeAboutKicker: 'Discover Salto',
        placeAboutTitle: 'About this place',
        placeEventsKicker: 'Local events',
        placeEventsTitle: 'Upcoming events at this place',
        placeHistoryTitle: 'History and heritage',
        placeHoursLabel: 'Hours',
        placeMapButton: 'Open location in Maps',
        placeNoEvents: 'There are no upcoming events at this place.',
        placeRecommendationsLabel: 'Recommendations',
        placeThingsTitle: 'Things to do and attractions',
        placeUsefulTitle: 'Useful information and location',
        placeViewMore: 'See more',
        pageHomeTitle: 'Home | SIGTUR',
        pageEventsTitle: 'Events | SIGTUR',
        pageTourismTitle: 'Tourism | SIGTUR',
        pagePlacesTitle: 'Places | SIGTUR',
        pageConfigTitle: 'Settings | SIGTUR',
        pageLoginTitle: 'Log in | SIGTUR',
        pageRegisterTitle: 'Register | SIGTUR',
        pageSupportTitle: 'Support | SIGTUR',
        pageTodosEventsTitle: 'All events | SIGTUR',
        loginTitle: 'Log in',
        loginIntro: 'Complete your details and register your account or log in.',
        loginUsername: 'Username / Email',
        loginUsernamePlaceholder: 'John Doe or john@example.com',
        loginPassword: 'Password',
        loginPasswordPlaceholder: '1234567',
        loginForgot: 'Forgot password',
        loginRegister: 'Register',
        loginSubmit: 'Log in',
        loginGoogle: 'Continue with Google',
        loginGoogleAlt: 'Continue with Google',
        loginLegal: 'By logging in you accept the company terms and conditions, the rules of use and the privacy policy.',
        registerTitle: 'Sign up',
        registerIntro: 'Complete your details to create your account and start enjoying the site.',
        registerName: 'Full name',
        registerNamePlaceholder: 'John Doe',
        registerEmail: 'Email',
        registerEmailPlaceholder: 'john@example.com',
        registerPassword: 'Password',
        registerPasswordPlaceholder: 'Name10394.',
        registerPhone: 'Phone number',
        registerPhonePlaceholder: '+1 555 123 456',
        registerSubmit: 'Register',
        registerLogin: 'Log in',
        registerGoogle: 'Continue with Google',
        registerGoogleAlt: 'Continue with Google',
        registerLegal: 'By signing up you accept the company terms and conditions, the rules of use and the privacy policy.',
        supportBack: '← Back',
        loginBack: '← Back to home',
        loginBackAlt: 'Back to home',
        supportEyebrow: 'Support center',
        supportTitle: 'We are here to help',
        supportLead: 'Find quick answers, submit a report or contact the support team to resolve any questions about the project.',
        supportFaqTitle: 'Frequently asked questions',
        supportFaqText: 'Quick answers to the most common questions.',
        supportReportTitle: 'Reports',
        supportReportText: 'Describe the problem so the team can review it.',
        supportContactTitle: 'Contact',
        supportContactText: 'Ways to reach support.',
        supportHelpTitle: 'Quick help',
        supportHelpText: 'Short guides to solve simple tasks.',
        supportFaq1Question: 'How do I access the panel?',
        supportFaq1Answer: 'Log in with your user credentials.',
        supportFaq2Question: 'Can I change the settings?',
        supportFaq2Answer: 'Yes, from the site settings section.',
        supportFaq3Question: 'How do I report an error?',
        supportFaq3Answer: 'Use the report form on this page.',
        supportReportSubject: 'Report subject',
        supportReportEmail: 'Your email',
        supportReportTextarea: 'Describe the problem or incident...',
        supportReportButton: 'Send report',
        supportContactEmail: 'Email: support@sigtur.com',
        supportContactWhatsapp: 'WhatsApp: 091 661 335',
        supportContactInstagram: 'Instagram: @bapstuy',
        supportContactHours: 'Hours: Monday to Friday, 9:00 to 18:00',
        supportHelp1Title: 'Update data',
        supportHelp1Text: 'Go to settings and save your changes.',
        supportHelp2Title: 'Check events',
        supportHelp2Text: 'Consult the events section and available filters.',
        supportHelp3Title: 'Resolve access issues',
        supportHelp3Text: 'Verify your username, password and connection.',
        todosEventsBack: '← Back to home',
        todosEventsSearch: 'Search events',
        todosEventsCategoryLabel: 'All',
        todosEventsFilterRecent: 'Most recent',
        todosEventsFilterRecommended: 'Recommended',
        todosEventsFilterFree: 'Free',
        todosEventsHeroLabel: 'Explore what is happening',
        todosEventsHeroTitle: 'All events',
        todosEventsHeroText: 'Discover cultural, sports and social activities in one place.',
        todosEventsCategoriesTitle: 'Races',
        todosEventsCategoriesCultural: 'Cultural',
        todosEventsCategoriesSport: 'Sports',
        todosEventsNavPrev: 'Previous events',
        todosEventsNavNext: 'Next events',
        eventsMainCategories: ['Sports', 'Discotheques', 'Competitions'],
        eventsPreviousTitle: 'Previous events',
        eventsReviewsTitle: 'Gallery',
        eventsGalleryTitle: 'Gallery',
        eventsViewAll: 'See all events',
        eventsViewPrevious: 'See previous events',
        eventsReviewNames: ['Benjamin R.', 'Aaron C.', 'Santiago D.', 'Federico S.', 'Pio M.'],
        tourismSectionEscapadasTitle: 'Recommended escapes',
        tourismSectionRoutesTitle: 'Routes and experiences',
        tourismDiscoverMore: 'Discover more',
        tourismHotelsTitle: 'Hotels',
        tourismHotelsSeeAll: 'See all hotels',
        tourismWaterparksTitle: 'Water parks and thermal baths',
        tourismEscapadasTitles: ['North waterfront', 'BaSalto', 'Parque Benito Solari'],
        tourismEscapadasDescriptions: [
            'A riverside walk to enjoy urban art, live music and sunsets by the water.',
            'Historic centre with monuments, cafés and cultural spaces that invite you to experience local identity.',
            'A green space where trails, picnics and outdoor sports blend together.'
        ],
        tourismEscapadasMeta: ['Great for families · 3 km', 'Culture · Centre', 'Nature · Relax'],
        tourismEscapadasAlt: ['Salto waterfront', 'Basalt', 'Parque Solari'],
        tourismRoutesTitles: ['La Trouville', 'Cine Sarandi', 'La Fosa', 'Salto Shopping'],
        tourismRoutesDescriptions: [
            'Try traditional dishes and discover local flavours in markets, grills and cafés with charm.',
            'Itineraries to experience the city at night with shows, bars and live music spots.',
            'A skate, bike and calisthenics circuit.',
            'A shopping centre with a variety of stores and dining options.'
        ],
        tourismRoutesMeta: ['Food', 'Cinema', 'Sports · Guided tour available', 'Shopping'],
        tourismRoutesAlt: ['Trouville', 'Cine Sarandi', 'La Fosa', 'Salto shopping'],
        tourismHotelNames: ['Salto Hotel & Casino', 'Hotel Eldorado', 'Hotel Español', 'Hotel Horacio Quiroga'],
        tourismHotelPrices: ['$230 per night', '$310 per night', '$185 per night', '$270 per night'],
        tourismHotelLocations: ['Address: 25 de Agosto 05', 'Address: Av. Sarandi 20', 'Address: Barrio Hipódromo', 'Address: Brasil 826'],
        tourismHotelStars4: '4 stars',
        tourismHotelStars5: '5 stars',
        tourismHotelAlt: ['Salto Hotel & Casino', 'Hotel Eldorado', 'Hotel Español', 'Hotel Horacio Quiroga'],
        tourismWaterparkTitles: ['Termas del Dayman', 'Acuamania', 'Termas de la Arapey', 'Agua Clara'],
        tourismWaterparkDescriptions: [
            'Natural thermal baths with hot springs.',
            'Water park with various attractions and rest areas.',
            'Thermal baths to relax with the family.',
            'Relaxing pools to enjoy during the holidays.'
        ],
        tourismWaterparkMeta: ['Thermal baths', 'Water park', 'Thermal baths', 'Thermal baths'],
        tourismWaterparkAlt: ['Dayman', 'Aquamania', 'La Arapey', 'Aguas Claras'],
        tourismGastronomyTitle: 'Gastronomy',
        tourismGastronomySeeAll: 'See all Salto gastronomy',
        tourismGalleryTitle: 'Tourist gallery',
        tourismGallerySeeAll: 'See all galleries',
        tourismGalleryIntro: 'Landscapes, walks and places to inspire your next visit.',
        tourismGalleryTitles: ['Benito Solari Park', 'Dayman Hot Springs', 'North Waterfront', 'Arapey Hot Springs', 'Artigas Square', 'Salto Shopping', 'Cine Sarandí', 'Litoral landscapes'],
        tourismGalleryAlt: ['Benito Solari Park', 'Pools at Dayman Hot Springs', 'Walk along the North Waterfront', 'Salto hot springs resort', 'Monuments at Artigas Square', 'Salto Shopping', 'Downtown street by Cine Sarandí', 'Natural landscape in the region'],
        placesSectionEmblematicTitle: 'Iconic places',
        placesSectionCategoriesTitle: 'Place categories',
        placesSectionLeisureTitle: 'Places to have fun',
        placesSectionGalleryTitle: 'Historical gallery',
        placesMoreButton: 'See more places',
        placesGalleryButton: 'See all galleries',
        placesEmblematicTitles: ['Plaza Treinta y Tres Orientales', 'Plaza Artigas', 'Parque Benito Solari'],
        placesEmblematicDescriptions: [
            'A calm riverside walk with green spaces, viewpoints and areas to rest outdoors.',
            'An emblematic central square with traditional architecture, cafés and permanent cultural activity.',
            'A large green area perfect for family walks, sports and outdoor events.'
        ],
        placesEmblematicAlt: ['South waterfront', 'Historic square', 'Parque Benito Solari'],
        placesCategoryTitles: ['Gastronomy', 'Culture', 'Nature', 'Experiences'],
        placesCategoryDescriptions: [
            'A selection of cafés, grills and local spots with flavours that shape the city.',
            'Galleries, murals and historic spaces that showcase Salto’s identity and heritage.',
            'Green areas, parks and natural paths for outdoor lovers and calm.',
            'Interactive activities, markets and meeting points that enrich every visit.'
        ],
        placesCategoryAlt: ['Cafés and gastronomy', 'Urban art', 'Natural spaces', 'Urban attractions'],
        placesLeisureTitles: ['Plaza Roosvelt', 'North waterfront', 'Muelle Negro'],
        placesLeisureDescriptions: [
            'A quiet plaza by the coast to enjoy mates with friends or family.',
            'A riverside area with views, green spaces and places for outdoor activities.',
            'A zone with river views to relax and spend time with friends.'
        ],
        placesLeisureAlt: ['Plaza Roosvelt', 'North waterfront', 'Muelle Negro'],
        placesGalleryIntro: 'A visual journey through the places and traditions that tell Salto’s story.',
        placesGalleryTitles: ['Plaza Treinta y Tres', 'Artigas Square', 'Historic downtown', 'Salto culture', 'Salto waterfront'],
        placesGalleryAlt: ['Plaza Treinta y Tres and its historic surroundings', 'Monuments at Artigas Square', 'Downtown street by Cine Sarandí', 'Expression of popular culture in Salto', 'Salto waterfront promenade']
    },
    pt: {
        navProfile: 'Perfil',
        navMenuOpen: 'Abrir menu',
        navSearch: 'Pesquisar',
        searchResultsTitle: 'Resultados para:',
        placeAboutKicker: 'Descubra Salto',
        placeAboutTitle: 'Sobre este lugar',
        placeEventsKicker: 'Agenda local',
        placeEventsTitle: 'Próximos eventos neste lugar',
        placeHistoryTitle: 'História e patrimônio',
        placeHoursLabel: 'Horários',
        placeMapButton: 'Abrir localização no Maps',
        placeNoEvents: 'Não há eventos próximos neste lugar.',
        placeRecommendationsLabel: 'Recomendações',
        placeThingsTitle: 'O que fazer e atrações',
        placeUsefulTitle: 'Informações úteis e localização',
        placeViewMore: 'Ver mais',
        pageHomeTitle: 'Início | SIGTUR',
        pageEventsTitle: 'Eventos | SIGTUR',
        pageTourismTitle: 'Turismo | SIGTUR',
        pagePlacesTitle: 'Locais | SIGTUR',
        pageConfigTitle: 'Configurações | SIGTUR',
        pageLoginTitle: 'Entrar | SIGTUR',
        pageRegisterTitle: 'Registrar-se | SIGTUR',
        pageSupportTitle: 'Suporte | SIGTUR',
        pageTodosEventsTitle: 'Todos os eventos | SIGTUR',
        loginTitle: 'Entrar',
        loginIntro: 'Preencha seus dados e registre sua conta ou faça login.',
        loginUsername: 'Usuário / E-mail',
        loginUsernamePlaceholder: 'João Silva ou joao@email.com',
        loginPassword: 'Senha',
        loginPasswordPlaceholder: '1234567',
        loginForgot: 'Esqueci minha senha',
        loginRegister: 'Registrar-se',
        loginSubmit: 'Entrar',
        loginGoogle: 'Continuar com Google',
        loginGoogleAlt: 'Continuar com Google',
        loginLegal: 'Ao entrar, você aceita os termos e condições da empresa, as regras de uso e a política de privacidade.',
        registerTitle: 'Cadastre-se',
        registerIntro: 'Preencha seus dados para criar sua conta e começar a aproveitar o site.',
        registerName: 'Nome completo',
        registerNamePlaceholder: 'João Silva',
        registerEmail: 'E-mail',
        registerEmailPlaceholder: 'joao@email.com',
        registerPassword: 'Senha',
        registerPasswordPlaceholder: 'Nome10394.',
        registerPhone: 'Número de telefone',
        registerPhonePlaceholder: '+598 99 123 456',
        registerSubmit: 'Registrar-se',
        registerLogin: 'Entrar',
        registerGoogle: 'Continuar com Google',
        registerGoogleAlt: 'Continuar com Google',
        registerLegal: 'Ao se registrar, você aceita os termos e condições da empresa, as regras de uso e a política de privacidade.',
        supportBack: '← Voltar',
        loginBack: '← Voltar ao início',
        loginBackAlt: 'Voltar ao início',
        supportEyebrow: 'Centro de suporte',
        supportTitle: 'Estamos aqui para ajudar',
        supportLead: 'Encontre respostas rápidas, envie um relatório ou entre em contato com a equipe de suporte para resolver qualquer dúvida sobre o projeto.',
        supportFaqTitle: 'Perguntas frequentes',
        supportFaqText: 'Respostas rápidas às dúvidas mais comuns.',
        supportReportTitle: 'Relatórios',
        supportReportText: 'Descreva o problema para que a equipe possa revisá-lo.',
        supportContactTitle: 'Contato',
        supportContactText: 'Opções para entrar em contato com o suporte.',
        supportHelpTitle: 'Ajuda rápida',
        supportHelpText: 'Guias curtos para resolver tarefas simples.',
        supportFaq1Question: 'Como acesso o painel?',
        supportFaq1Answer: 'Faça login com suas credenciais de usuário.',
        supportFaq2Question: 'Posso alterar a configuração?',
        supportFaq2Answer: 'Sim, na seção de configurações do site.',
        supportFaq3Question: 'Como reporto um erro?',
        supportFaq3Answer: 'Use o formulário de relatórios nesta mesma página.',
        supportReportSubject: 'Assunto do relatório',
        supportReportEmail: 'Seu e-mail',
        supportReportTextarea: 'Descreva o problema ou a ocorrência...',
        supportReportButton: 'Enviar relatório',
        supportContactEmail: 'E-mail: suporte@sigtur.com',
        supportContactWhatsapp: 'WhatsApp: 091 661 335',
        supportContactInstagram: 'Instagram: @bapstuy',
        supportContactHours: 'Horário: segunda a sexta, 9:00 às 18:00',
        supportHelp1Title: 'Atualizar dados',
        supportHelp1Text: 'Vá em configurações e salve as alterações.',
        supportHelp2Title: 'Revisar eventos',
        supportHelp2Text: 'Consulte a seção de eventos e os filtros disponíveis.',
        supportHelp3Title: 'Resolver acesso',
        supportHelp3Text: 'Verifique seu usuário, senha e conexão.',
        tourismSectionEscapadasTitle: 'Escapadas recomendadas',
        tourismSectionRoutesTitle: 'Rotas e experiências',
        tourismDiscoverMore: 'Descubra mais',
        tourismHotelsTitle: 'Hotéis',
        tourismHotelsSeeAll: 'Ver todos os hotéis',
        tourismWaterparksTitle: 'Parques aquáticos e termas',
        tourismEscapadasTitles: ['Costanera Norte', 'BaSalto', 'Parque Benito Solari'],
        tourismEscapadasDescriptions: [
            'Um passeio à beira do rio para aproveitar arte urbana, música ao vivo e pôr do sol junto à água.',
            'Centro histórico com monumentos, cafés e espaços culturais que convidam a conhecer a identidade local.',
            'Um espaço verde onde trilhas, piqueniques e esportes ao ar livre se misturam.'
        ],
        tourismEscapadasMeta: ['Ideal para famílias · 3 km', 'Cultura · Centro', 'Natureza · Relax'],
        tourismEscapadasAlt: ['Costanera de Salto', 'Basalto', 'Parque Solari'],
        tourismRoutesTitles: ['La Trouville', 'Cine Sarandi', 'La Fosa', 'Salto Shopping'],
        tourismRoutesDescriptions: [
            'Experimente pratos típicos e descubra sabores locais em mercados, churrascarias e cafés com charme.',
            'Itinerários para viver a cidade à noite com espetáculos, bares e música ao vivo.',
            'Circuito de skate, bicicleta e calistenia.',
            'Um shopping com variedade de lojas e opções de alimentação.'
        ],
        tourismRoutesMeta: ['Comida', 'Cinema', 'Esportes · Guia disponível', 'Shopping'],
        tourismRoutesAlt: ['Trouville', 'Cine Sarandi', 'La Fosa', 'Shopping de Salto'],
        tourismHotelNames: ['Salto Hotel & Casino', 'Hotel Eldorado', 'Hotel Español', 'Hotel Horacio Quiroga'],
        tourismHotelPrices: ['R$ 230 por noite', 'R$ 310 por noite', 'R$ 185 por noite', 'R$ 270 por noite'],
        tourismHotelLocations: ['Endereço: 25 de Agosto 05', 'Endereço: Av. Sarandi 20', 'Endereço: Barrio Hipódromo', 'Endereço: Brasil 826'],
        tourismHotelStars4: '4 estrelas',
        tourismHotelStars5: '5 estrelas',
        tourismHotelAlt: ['Salto Hotel & Casino', 'Hotel Eldorado', 'Hotel Español', 'Hotel Horacio Quiroga'],
        tourismWaterparkTitles: ['Termas del Dayman', 'Acuamania', 'Termas de la Arapey', 'Agua Clara'],
        tourismWaterparkDescriptions: [
            'Termas naturais com águas termais.',
            'Parque aquático com diversas atrações e áreas de descanso.',
            'Termas para relaxar em família.',
            'Piscinas relaxantes para aproveitar nas férias.'
        ],
        tourismWaterparkMeta: ['Termas', 'Parque aquático', 'Termas', 'Termas'],
        tourismWaterparkAlt: ['Dayman', 'Aquamania', 'La Arapey', 'Aguas Claras'],
        tourismGastronomyTitle: 'Gastronomia',
        tourismGastronomySeeAll: 'Ver toda a gastronomia de Salto',
        tourismGalleryTitle: 'Galeria turística',
        tourismGallerySeeAll: 'Ver todas as galerias',
        tourismGalleryIntro: 'Paisagens, passeios e lugares para inspirar sua próxima visita.',
        tourismGalleryTitles: ['Parque Benito Solari', 'Termas del Dayman', 'Costanera Norte', 'Termas de Arapey', 'Praça Artigas', 'Shopping Salto', 'Cine Sarandí', 'Paisagens do litoral'],
        tourismGalleryAlt: ['Parque Solari', 'Piscinas das Termas del Dayman', 'Passeio pela Costanera Norte', 'Complexo termal de Salto', 'Monumentos da Praça Artigas', 'Shopping Salto', 'Rua central junto ao Cine Sarandí', 'Paisagem natural da região'],
        placesSectionEmblematicTitle: 'Lugares emblemáticos',
        placesSectionCategoriesTitle: 'Categorias de lugares',
        placesSectionLeisureTitle: 'Lugares para se divertir',
        placesSectionGalleryTitle: 'Galeria histórica',
        placesMoreButton: 'Ver mais lugares',
        placesGalleryButton: 'Ver todas as galerias',
        placesEmblematicTitles: ['Praça Treinta y Tres Orientales', 'Praça Artigas', 'Parque Benito Solari'],
        placesEmblematicDescriptions: [
            'Um passeio tranquilo à beira do rio com áreas verdes, mirantes e locais para descansar ao ar livre.',
            'Uma praça central emblemática com arquitetura tradicional, cafés e atividade cultural permanente.',
            'Uma grande área verde perfeita para caminhadas em família, esportes e eventos ao ar livre.'
        ],
        placesEmblematicAlt: ['Costanera Sul', 'Praça histórica', 'Parque Benito Solari'],
        placesCategoryTitles: ['Gastronomia', 'Cultura', 'Natureza', 'Experiências'],
        placesCategoryDescriptions: [
            'Uma seleção de cafés, churrascarias e locais com sabores que marcam a cidade.',
            'Galerias, murais e espaços históricos que mostram a identidade e o patrimônio de Salto.',
            'Áreas verdes, parques e trilhas naturais para os amantes do ar livre e da calma.',
            'Atividades interativas, mercados e pontos de encontro que enriquecem cada visita.'
        ],
        placesCategoryAlt: ['Cafés e gastronomia', 'Arte urbana', 'Espaços naturais', 'Atrações urbanas'],
        placesLeisureTitles: ['Praça Roosvelt', 'Costanera Norte', 'Muelle Negro'],
        placesLeisureDescriptions: [
            'Uma praça tranquila na costa para tomar chimarrão com amigos ou família.',
            'Uma área costeira com vista para o rio, áreas verdes e espaços para atividades ao ar livre.',
            'Uma zona com vista para o rio para relaxar e passar o tempo com amigos.'
        ],
        placesLeisureAlt: ['Praça Roosvelt', 'Costanera Norte', 'Muelle Negro'],
        placesGalleryIntro: 'Um percurso visual pelos espaços e tradições que contam a história de Salto.',
        placesGalleryTitles: ['Praça Treinta y Tres', 'Praça Artigas', 'Centro histórico', 'Cultura salteña', 'Costanera de Salto'],
        placesGalleryAlt: ['Praça Treinta y Tres e seu entorno histórico', 'Monumentos da Praça Artigas', 'Rua central junto ao Cine Sarandí', 'Expressão da cultura popular salteña', 'Passeio costeiro de Salto'],
        todosEventsBack: '← Voltar ao início',
        todosEventsSearch: 'Buscar eventos',
        todosEventsCategoryLabel: 'Todas',
        todosEventsFilterRecent: 'Mais recentes',
        todosEventsFilterRecommended: 'Recomendados',
        todosEventsFilterFree: 'Grátis',
        todosEventsHeroLabel: 'Explore o que está acontecendo',
        todosEventsHeroTitle: 'Todos os eventos',
        todosEventsHeroText: 'Descubra atividades culturais, esportivas e sociais em um só lugar.',
        todosEventsCategoriesTitle: 'Corridas',
        todosEventsCategoriesCultural: 'Culturais',
        todosEventsCategoriesSport: 'Esportivos',
        todosEventsNavPrev: 'Eventos anteriores',
        todosEventsNavNext: 'Próximos eventos',
        eventsMainCategories: ['Esportivos', 'Discotecas', 'Competições'],
        eventsPreviousTitle: 'Eventos anteriores',
        eventsReviewsTitle: 'Galeria',
        eventsGalleryTitle: 'Galeria',
        eventsViewAll: 'Ver todos os eventos',
        eventsViewPrevious: 'Ver eventos anteriores',
        eventsReviewNames: ['Benjamim R.', 'Aaron C.', 'Santiago D.', 'Federico S.', 'Pío M.']
    }
};

Object.entries(traduccionesExtra).forEach(([idioma, valores]) => {
    if (traducciones[idioma]) {
        Object.assign(traducciones[idioma], valores);
    }
});

// Persistimos la preferencia para restaurarla automáticamente entre páginas.
const guardarTema = (oscuro) => {
    localStorage.setItem(storageClaveTema, oscuro ? '1' : '0'); // persistencia en el navegador
};

const leerTema = () => {
    return localStorage.getItem(storageClaveTema) === '1'; // true si ya habiamos dejado oscuro
};

const leerIdioma = () => {
    const guardado = localStorage.getItem(storageClaveIdioma);
    return idiomasDisponibles.includes(guardado) ? guardado : 'es';
};

const guardarIdioma = (idioma) => {
    const idiomaActivo = idiomasDisponibles.includes(idioma) ? idioma : 'es';
    localStorage.setItem(storageClaveIdioma, idiomaActivo);
};

const aplicarIdioma = (idioma = leerIdioma(), persistir = true) => {
    const idiomaActivo = idiomasDisponibles.includes(idioma) ? idioma : 'es';
    if (persistir) guardarIdioma(idiomaActivo);
    document.documentElement.lang = idiomaActivo;
    document.documentElement.setAttribute('data-lang', idiomaActivo);
    document.body.dataset.lang = idiomaActivo;

    const traduccion = traducciones[idiomaActivo] || traducciones.es;

    document.querySelectorAll('[data-i18n]').forEach((elemento) => {
        if (!elemento.dataset.translatorSource && elemento.children.length === 0 && elemento.textContent.trim()) {
            elemento.dataset.translatorSource = elemento.textContent.trim();
        }
        const clave = elemento.getAttribute('data-i18n');
        const valor = traduccion[clave] ?? traducciones.es[clave] ?? traducciones.en[clave];
        if (!valor) return;
        if (elemento.tagName === 'INPUT' || elemento.tagName === 'TEXTAREA') {
            if (elemento.getAttribute('data-i18n-placeholder')) {
                elemento.placeholder = valor;
                elemento.dataset.translatorLocalPlaceholderLanguage = idiomaActivo;
            }
            return;
        }
        elemento.dataset.translatorLocalTextLanguage = idiomaActivo;
        if (Array.isArray(valor)) {
            const indice = parseInt(elemento.getAttribute('data-i18n-index') || '0', 10);
            elemento.textContent = valor[indice] || valor[0];
            return;
        }
        elemento.textContent = valor;
    });

    document.querySelectorAll('[data-i18n-placeholder]').forEach((elemento) => {
        if (!elemento.dataset.translatorPlaceholderSource && elemento.hasAttribute('placeholder')) {
            elemento.dataset.translatorPlaceholderSource = elemento.getAttribute('placeholder');
        }
        const clave = elemento.getAttribute('data-i18n-placeholder');
        const valor = traduccion[clave] ?? traducciones.es[clave] ?? traducciones.en[clave];
        if (valor) {
            elemento.placeholder = valor;
            elemento.dataset.translatorLocalPlaceholderLanguage = idiomaActivo;
        }
    });

    document.querySelectorAll('[data-i18n-title]').forEach((elemento) => {
        if (!elemento.dataset.translatorTitleSource && elemento.textContent.trim()) {
            elemento.dataset.translatorTitleSource = elemento.textContent.trim();
        }
        const clave = elemento.getAttribute('data-i18n-title');
        const valor = traduccion[clave] ?? traducciones.es[clave] ?? traducciones.en[clave];
        if (valor) {
            elemento.textContent = valor;
            elemento.dataset.translatorLocalTitleLanguage = idiomaActivo;
            if (elemento.tagName === 'TITLE') {
                document.title = valor;
            }
        }
    });

    document.querySelectorAll('[data-i18n-aria-label]').forEach((elemento) => {
        const clave = elemento.getAttribute('data-i18n-aria-label');
        const valor = traduccion[clave] ?? traducciones.es[clave] ?? traducciones.en[clave];
        if (valor) {
            elemento.setAttribute('aria-label', valor);
            elemento.dataset.translatorLocalAriaLabelLanguage = idiomaActivo;
        }
    });

    document.querySelectorAll('[data-i18n-alt]').forEach((elemento) => {
        if (!elemento.dataset.translatorAltSource && elemento.hasAttribute('alt')) {
            elemento.dataset.translatorAltSource = elemento.getAttribute('alt');
        }
        const clave = elemento.getAttribute('data-i18n-alt');
        const valor = traduccion[clave] ?? traducciones.es[clave] ?? traducciones.en[clave];
        if (valor) {
            elemento.setAttribute('alt', valor);
            elemento.dataset.translatorLocalAltLanguage = idiomaActivo;
        }
    });

    document.querySelectorAll('option[data-i18n]').forEach((option) => {
        const clave = option.getAttribute('data-i18n');
        const valor = traduccion[clave] ?? traducciones.es[clave] ?? traducciones.en[clave];
        if (valor) {
            option.textContent = valor;
            option.dataset.translatorLocalTextLanguage = idiomaActivo;
        }
    });

    const selectorIdioma = document.getElementById('idioma');
    if (selectorIdioma) {
        selectorIdioma.value = idiomaActivo;
    }

    document.dispatchEvent(new CustomEvent('sigtur:language-changed', {
        detail: { language: idiomaActivo }
    }));
};

// chequea si el nodo esta dentro de la barra de navegacion o el perfil
const esNav = (elemento) => {
    return !!elemento.closest('header, nav, .menu, .bottom-nav, .perfil-menu');
};

// chequea si es el boton de informacion, para no tocarlo con el modo oscuro
const esInfo = (elemento) => {
    return elemento && elemento.classList.contains('info-btn');
};

/* El tema recorre componentes compartidos y excluye navegación/paneles con
    identidad propia para no sobrescribir estilos específicos. */
const aplicarTema = (oscuro, botonModoOscuro) => {
    const colorTexto = oscuro ? '#ffffff' : ''; // si estamos en modo oscuro, el texto debe ser blanco
    const fondoBoton = oscuro ? 'rgba(255,255,255,0.12)' : ''; // fondo tenue para botones menos importantes

    body.dataset.tema = oscuro ? 'oscuro' : 'claro'; // marca el tema actual para CSS

    const features = document.querySelectorAll('.feature, .feature-header');
    features.forEach((feature) => {
        setStyle(feature, 'backgroundColor', oscuro ? '#000000' : '');
        setStyle(feature, 'color', oscuro ? '#ffffff' : '');
    });

    if (menu) {
        setStyle(menu, 'backgroundColor', oscuro ? 'rgba(0, 0, 0, 0.38)' : 'rgba(12, 18, 24, 0.24)');
    }

    if (isIndex) {
        // index no tiene <main>, por eso usamos body para seleccionar los textos
        const textos = document.querySelectorAll('body h1, body h2, body h3, body h4, body h5, body h6, body p, body span, body a, body label, body li, body small');
        textos.forEach((texto) => {
            if (esNav(texto)) return; // no tocamos nada dentro de la barra de navegacion
            if (esInfo(texto)) return; // no tocamos el boton de info y su panel
            if (texto.closest('.feature')) return; // excluyo todo el bloque de feature para que siga en su estilo original
            if (texto.closest('.info-panel')) return; // mantengo el texto del panel siempre oscuro
            setStyle(texto, 'color', colorTexto); // cambio el color de texto al modo oscuro
        });

        if (infoPanel) {
            const infoTextos = infoPanel.querySelectorAll('h3, p');
            infoTextos.forEach((texto) => {
                setStyle(texto, 'color', '#111'); // el texto del panel siempre queda negro
            });
        }
    }

    const flechas = document.querySelectorAll('.flecha');
    flechas.forEach((flecha) => {
        setStyle(flecha, 'backgroundColor', oscuro ? '#ffffff' : '#111111');
        setStyle(flecha, 'color', oscuro ? '#cccccc' : '#111111');
    });
    
    const indicadoresActivos = document.querySelectorAll('main.pagina-eventos .indicador-netflix.activo');
    indicadoresActivos.forEach((indicador) => {
        setStyle(indicador, 'backgroundColor', oscuro ? '#ffffff' : '#111111');
    });

    if (botonModoOscuro) {
        setStyle(botonModoOscuro, 'background', oscuro ? '#ffffff' : '#111111');
        setStyle(botonModoOscuro, 'color', '#111111');
        setStyle(botonModoOscuro, 'borderColor', oscuro ? '#111111' : '#ffffff');
    }

    if (isEventos) {
        // en eventos cambiamos el h2 de la galería y las cabeceras de seccion según el tema
        const titulosGaleria = document.querySelectorAll('main.pagina-eventos .galeria-eventos h2');
        titulosGaleria.forEach((titulo) => {
            setStyle(titulo, 'color', oscuro ? '#ffffff' : '#111111');
        });

        const cabecerasSeccion = document.querySelectorAll('main.pagina-eventos .encabezado-categoria h3');
        cabecerasSeccion.forEach((titulo) => {
            setStyle(titulo, 'color', colorTexto);
        });

        const indicadoresActivos = document.querySelectorAll('main.pagina-eventos .indicador-netflix.activo');
        indicadoresActivos.forEach((indicador) => {
            setStyle(indicador, 'backgroundColor', oscuro ? '#ffffff' : '#111111');
        });
    } else if (isTurismo || isLugares) {
        // en turismo/lugares aplico el cambio a todos los h2, porque esas paginas no tienen excepcion especial
        const selector = isTurismo
            ? 'main.pagina-turismo h2'
            : 'main.pagina-lugares h2';
        const titulos = document.querySelectorAll(selector);
        titulos.forEach((titulo) => {
            setStyle(titulo, 'color', colorTexto);
        });
    }

    const botones = document.querySelectorAll('button');
    botones.forEach((boton) => {
        if (esInfo(boton) || esNav(boton)) {
            setStyle(boton, 'backgroundColor', '');
            return; // no cambiamos botones de nav ni info
        }
        if (boton.classList.contains('feature-toggle')) {
            setStyle(boton, 'backgroundColor', oscuro ? '#000000' : '');
            setStyle(boton, 'color', oscuro ? '#ffffff' : '');
            return;
        }
        setStyle(boton, 'backgroundColor', fondoBoton); // damos un fondo tenue a los botones del contenido
    });

    if (botonModoOscuro) {
        botonModoOscuro.textContent = oscuro ? 'Modo Claro' : 'Modo Oscuro'; // muestro la etiqueta correcta en el boton
    }
};

const activarPerfil = () => {
    if (!botonPerfil || !perfil) return;
    botonPerfil.addEventListener('click', () => {
        perfil.classList.toggle('activo'); // abre/cierra el perfil
    });
};

/* Este módulo controla el modal de perfil, el menú responsive y las preferencias
    globales; se mantiene en un único script porque varias páginas lo comparten. */
const activarModalPerfil = () => {
    const modal = document.getElementById('perfilModal');
    if (!modal) return;

    const abrir = document.querySelector('[data-open-profile-modal]');
    const cerrar = document.querySelectorAll('[data-close-profile-modal]');
    const inputAvatar = document.getElementById('avatarInput');
    const preview = document.getElementById('avatarPreview');

    abrir?.addEventListener('click', (event) => {
        event.preventDefault();
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
    });

    cerrar.forEach((boton) => {
        boton.addEventListener('click', () => {
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
        });
    });

    // El backdrop cierra el modal solo cuando el clic no ocurrió dentro del diálogo.
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
        }
    });

    if (inputAvatar && preview) {
        inputAvatar.addEventListener('change', () => {
            const archivo = inputAvatar.files?.[0];
            if (!archivo) return;
            preview.src = URL.createObjectURL(archivo);
        });
    }
};

const activarHamburguesa = () => {
    if (!hamburguesa || !menu) return;
    hamburguesa.addEventListener('click', () => {
        menu.classList.toggle('activo'); // abre/cierra menu movil
    });
};

const activarNavAutoHide = () => {
    if (!menuPrincipal) return;

    let ultimoScroll = window.pageYOffset;
    let navOculto = false;

    const mostrarNav = () => {
        if (!navOculto) return;
        menuPrincipal.classList.remove('nav-hidden');
        navOculto = false;
    };

    const ocultarNav = () => {
        if (navOculto) return;
        menuPrincipal.classList.add('nav-hidden');
        navOculto = true;
    };

    // Ocultamos la navegación al bajar y la devolvemos al subir para maximizar espacio útil.
    window.addEventListener('scroll', () => {
        const actual = window.pageYOffset;
        if (actual > 150 && actual > ultimoScroll + 10) {
            ocultarNav();
        }
        ultimoScroll = actual;
    }, { passive: true });

    window.addEventListener('mousemove', (event) => {
        if (window.pageYOffset <= 10 || (window.pageYOffset > 150 && event.clientY <= 10)) {
            mostrarNav();
        }
    });

    window.addEventListener('touchstart', (event) => {
        const touch = event.touches?.[0];
        if (window.pageYOffset <= 10 || (window.pageYOffset > 150 && touch?.clientY <= 10)) {
            mostrarNav();
        }
    });
};

const activarBotonUsuarioInferior = () => {
    if (!bottomNavUsuario || !perfil) return;
    bottomNavUsuario.addEventListener('click', (evento) => {
        evento.preventDefault();
        perfil.classList.toggle('activo');
    });
};

const cerrarClickAfuera = () => {
    document.addEventListener('click', (evento) => {
        const nodo = evento.target;
        if (perfil && botonPerfil && !perfil.contains(nodo) && !botonPerfil.contains(nodo)) {
            perfil.classList.remove('activo');
        }
        if (menu && hamburguesa && !menu.contains(nodo) && !hamburguesa.contains(nodo)) {
            menu.classList.remove('activo');
        }
    });
};

const activarCarrusel = () => {
    document.querySelectorAll('.carrusel[data-carrusel]').forEach((carrusel) => {
        const seccion = carrusel.closest('[data-carrusel-seccion]');
        const slides = carrusel.querySelectorAll('.slide');
        const indicadores = seccion?.querySelectorAll('.indicador') || [];
        const flechaIzquierda = carrusel.querySelector('.flecha.izquierda');
        const flechaDerecha = carrusel.querySelector('.flecha.derecha');
        let indiceActivo = 0;

        const cambiarSlide = (nuevoIndice) => {
            if (slides.length === 0 || indicadores.length === 0) return;
            slides[indiceActivo].classList.remove('activo');
            indicadores[indiceActivo].classList.remove('activo');
            indiceActivo = (nuevoIndice + slides.length) % slides.length;
            slides[indiceActivo].classList.add('activo');
            indicadores[indiceActivo].classList.add('activo');
        };

        flechaIzquierda?.addEventListener('click', () => {
            cambiarSlide(indiceActivo - 1);
        });
        flechaDerecha?.addEventListener('click', () => {
            cambiarSlide(indiceActivo + 1);
        });
        indicadores.forEach((indicador, index) => {
            indicador.addEventListener('click', () => {
                cambiarSlide(index);
            });
        });
    });
};

const activarToggleFeature = () => {
    const togglesFeature = document.querySelectorAll('.feature-toggle');
    const features = document.querySelectorAll('.feature');
    const cambiarEstadoFeature = (feature, toggle, abrir) => {
        if (!feature || !toggle) return;
        feature.classList.toggle('activo', abrir);
        feature.classList.toggle('open', abrir);
        toggle.classList.toggle('open', abrir);
        toggle.setAttribute('aria-expanded', String(abrir));
    };

    features.forEach((feature) => {
        cambiarEstadoFeature(feature, feature.querySelector('.feature-toggle'), false);
    });

    togglesFeature.forEach((toggle) => {
        const feature = toggle.closest('.feature');
        toggle.addEventListener('click', (evento) => {
            const boton = evento.currentTarget;
            const feature = boton.closest('.feature');
            const expandido = boton.getAttribute('aria-expanded') === 'true';
            const abrir = !expandido;
            if (!feature) return;
            if (abrir) {
                togglesFeature.forEach((otroToggle) => {
                    if (otroToggle === boton) return;
                    cambiarEstadoFeature(otroToggle.closest('.feature'), otroToggle, false);
                });
            }
            cambiarEstadoFeature(feature, boton, abrir);
        });
        feature?.addEventListener('click', (evento) => {
            if (evento.target.closest('.feature-toggle')) return;
            toggle.click();
        });
    });
};

const activarToggleFooter = () => {
    const togglesFooter = document.querySelectorAll('.footer-toggle');
    const breakpointFooterDesktop = window.matchMedia('(min-width: 781px)');

    const sincronizarTogglesFooter = (esEscritorio) => {
        togglesFooter.forEach((toggle) => {
            const footerCol = toggle.closest('.footer-col');
            if (!footerCol) return;
            if (footerCol.classList.contains('footer-col--expandable')) return;
            footerCol.classList.toggle('open', esEscritorio);
            toggle.classList.toggle('open', esEscritorio);
            toggle.setAttribute('aria-expanded', String(esEscritorio));
        });
    };

    sincronizarTogglesFooter(breakpointFooterDesktop.matches);
    breakpointFooterDesktop.addEventListener('change', (evento) => {
        sincronizarTogglesFooter(evento.matches);
    });

    togglesFooter.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const footerCol = toggle.closest('.footer-col');
            if (breakpointFooterDesktop.matches && (!footerCol || !footerCol.classList.contains('footer-col--expandable'))) return;
            const expandido = toggle.getAttribute('aria-expanded') === 'true';
            const abrir = !expandido;
            if (!footerCol) return;
            footerCol.classList.toggle('open', abrir);
            toggle.classList.toggle('open', abrir);
            toggle.setAttribute('aria-expanded', String(abrir));
        });
    });
};

const activarInformacionBapst = () => {
    const footerInfoButton = document.querySelector('.footer-info-button');
    const footerBapstButton = document.querySelector('.footer-bapst-button');
    if (!footerInfoButton && !footerBapstButton) return;

    const modal = document.createElement('div');
    modal.className = 'bapst-modal';
    modal.hidden = true;
    modal.innerHTML = `
        <div class="bapst-modal__backdrop" data-bapst-close></div>
        <section class="bapst-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="bapst-modal-title">
            <button class="bapst-modal__close" type="button" aria-label="Cerrar información" data-bapst-close>×</button>
            <span class="bapst-modal__eyebrow">Proyecto estudiantil</span>
            <h2 id="bapst-modal-title">BAPST</h2>
            <div class="bapst-modal__content"></div>
        </section>`;
    document.body.appendChild(modal);

    const eyebrow = modal.querySelector('.bapst-modal__eyebrow');
    const title = modal.querySelector('#bapst-modal-title');
    const content = modal.querySelector('.bapst-modal__content');

    const cerrar = () => {
        modal.hidden = true;
        document.body.classList.remove('bapst-modal-open');
    };

    const abrir = ({ eyebrowText, titleText, paragraphs, link }) => {
        if (eyebrow) eyebrow.textContent = eyebrowText;
        if (title) title.textContent = titleText;
        if (content) {
            content.innerHTML = paragraphs.map((texto) => `<p>${texto}</p>`).join('');
            if (link) {
                content.insertAdjacentHTML('beforeend', `<a href="${link.href}" target="_blank" rel="noopener noreferrer">${link.label}</a>`);
            }
        }
        modal.hidden = false;
        document.body.classList.add('bapst-modal-open');
        modal.querySelector('.bapst-modal__close')?.focus();
    };

    footerInfoButton?.addEventListener('click', () => {
        abrir({
            eyebrowText: 'Información',
            titleText: 'SIGTUR',
            paragraphs: [
                'SIGTUR es una guía local de Salto pensada para ayudarte a descubrir eventos, lugares y experiencias únicas de la ciudad.',
                'La plataforma reúne recomendaciones culturales, turísticas y de ocio para que cada visita sea más simple, informada y memorable.'
            ],
            link: null
        });
    });

    footerBapstButton?.addEventListener('click', () => {
        abrir({
            eyebrowText: 'Proyecto estudiantil',
            titleText: 'BAPST',
            paragraphs: [
                'Somos estudiantes de 3ro MC de Informática de la Escuela Catalina Harriague de Castaños de Salto.',
                'El equipo está formado por Benjamín Reina, Aaron Crespo, Santiago Diez, Pío Monetta y Federico Sarmiento.',
                'Diseñamos y desarrollamos esta página como una propuesta para conectar a la comunidad con lo mejor de nuestra ciudad.'
            ],
            link: {
                href: 'https://instagram.com/bapstuy',
                label: 'Conocer BAPST en Instagram'
            }
        });
    });

    modal.addEventListener('click', (evento) => {
        if (evento.target.closest('[data-bapst-close]')) cerrar();
    });
    document.addEventListener('keydown', (evento) => {
        if (evento.key === 'Escape' && !modal.hidden) cerrar();
    });
};

const activarInfo = () => {
    if (!infoButton || !infoPanel) return;
    infoButton.addEventListener('click', () => {
        const expandido = infoButton.getAttribute('aria-expanded') === 'true';
        const abrir = !expandido;
        infoButton.setAttribute('aria-expanded', String(abrir));
        infoPanel.classList.toggle('open', abrir);
    });
};

activarPerfil();
aplicarTema(leerTema(), null);
aplicarIdioma(leerIdioma());
activarHamburguesa();
activarNavAutoHide();
activarBotonUsuarioInferior();
cerrarClickAfuera();
activarCarrusel();
activarToggleFeature();
activarToggleFooter();
activarInformacionBapst();
activarInfo();
