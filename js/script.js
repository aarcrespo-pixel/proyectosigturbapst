const perfil = document.querySelector(".perfil"); // caja del perfil
const botonPerfil = document.querySelector(".perfil-btn"); // boton del perfil
const perfilMenu = document.querySelector(".perfil-menu"); // menu desplegable del perfil
const hamburguesa = document.querySelector(".hamburguesa"); // boton menu movil
const menu = document.querySelector(".menu"); // barra principal del menu
const body = document.body; // cuerpo del html

const isIndex = !document.querySelector("main.pagina-eventos, main.pagina-turismo, main.pagina-lugares"); // pagina principal, usa body en vez de main en el index
const isEventos = !!document.querySelector("main.pagina-eventos"); // pagina de eventos
const isTurismo = !!document.querySelector("main.pagina-turismo"); // pagina de turismo
const isLugares = !!document.querySelector("main.pagina-lugares"); // pagina de lugares

const infoButton = document.querySelector('.info-btn'); // boton info en el index
const infoPanel = document.querySelector('.info-panel'); // panel info en el index
const bottomNavUsuario = document.querySelector('.bottom-nav-item[href$="login.html"]'); // boton usuario en menu inferior

const slides = document.querySelectorAll('.slide'); // slides del carrusel
const indicadores = document.querySelectorAll('.indicador'); // puntos del carrusel
const flechaIzquierda = document.querySelector('.flecha.izquierda'); // flecha anterior
const flechaDerecha = document.querySelector('.flecha.derecha'); // flecha siguiente
let indiceActivo = 0; // index de slide activo

const setStyle = (elemento, propiedad, valor) => {
    if (elemento) {
        elemento.style[propiedad] = valor; // cambia estilo directo con .style
    }
};

const storageClaveTema = 'modo-oscuro'; // guardamos si estamos en dark mode o no
const storageClaveIdioma = 'sigtur-idioma';
const idiomasDisponibles = ['es', 'en', 'pt', 'fr', 'it', 'de', 'ja', 'zh', 'ar', 'ru'];

const traducciones = {
    es: {
        navHome: 'Inicio',
        navEvents: 'Eventos',
        navTourism: 'Turismo',
        navPlaces: 'Lugares',
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
        homeEventsTitle: 'Eventos Destacados',
        homeEventButton: 'Ver más',
        homeNewsTitle: 'Noticias en Salto',
        homeNewsButton: 'Ver noticia',
        footerEvents: 'EVENTOS',
        footerPlaces: 'LUGARES',
        footerTourism: 'TURISMO',
        footerSigtur: 'SIGTUR',
        footerEventLinks: ['Eventos destacados', 'Próximos eventos', 'Eventos anteriores', 'Todos los eventos'],
        footerPlaceLinks: ['Plaza Artigas', 'Plaza Treinta y Tres Orientales', 'Costanera Norte', 'Costanera Sur', 'Parque Benito Solari'],
        footerTourismLinks: ['Hoteles', 'Actividades', 'Lugares turísticos', 'Experiencias'],
        footerLegal: 'SIGTUR © 2026 by Bapst is licensed under CC BY-NC-ND 4.0',
        configBack: '← Volver',
        configEyebrow: '⚙️ Configuración',
        configTitle: 'Personaliza la experiencia de tu sitio',
        configDescription: 'Aquí tienes una base visual y estructural para administrar los datos del sitio, las preferencias de usuario y las opciones de apariencia.',
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
        homeEventsTitle: 'Featured Events',
        homeEventButton: 'See more',
        homeNewsTitle: 'News in Salto',
        homeNewsButton: 'Read more',
        footerEvents: 'EVENTS',
        footerPlaces: 'PLACES',
        footerTourism: 'TOURISM',
        footerSigtur: 'SIGTUR',
        footerEventLinks: ['Featured events', 'Upcoming events', 'Past events', 'All events'],
        footerPlaceLinks: ['Plaza Artigas', 'Plaza Treinta y Tres Orientales', 'North waterfront', 'South waterfront', 'Parque Benito Solari'],
        footerTourismLinks: ['Hotels', 'Activities', 'Tourist spots', 'Experiences'],
        footerLegal: 'SIGTUR © 2026 by Bapst is licensed under CC BY-NC-ND 4.0',
        configBack: '← Back',
        configEyebrow: '⚙️ Settings',
        configTitle: 'Personalize your site experience',
        configDescription: 'Here you will find a visual and structural base to manage site data, user preferences and appearance options.',
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
        homeEventsTitle: 'Eventos em destaque',
        homeEventButton: 'Ver mais',
        homeNewsTitle: 'Notícias em Salto',
        homeNewsButton: 'Ver notícia',
        footerEvents: 'EVENTOS',
        footerPlaces: 'LOCAIS',
        footerTourism: 'TURISMO',
        footerSigtur: 'SIGTUR',
        footerEventLinks: ['Eventos em destaque', 'Próximos eventos', 'Eventos anteriores', 'Todos os eventos'],
        footerPlaceLinks: ['Praça Artigas', 'Praça Treinta y Tres Orientales', 'Costanera Norte', 'Costanera Sul', 'Parque Benito Solari'],
        footerTourismLinks: ['Hotéis', 'Atividades', 'Locais turísticos', 'Experiências'],
        footerLegal: 'SIGTUR © 2026 por Bapst está licenciado sob CC BY-NC-ND 4.0',
        configBack: '← Voltar',
        configEyebrow: '⚙️ Configurações',
        configTitle: 'Personalize a experiência do seu site',
        configDescription: 'Aqui você tem uma base visual e estrutural para administrar os dados do site, as preferências do usuário e as opções de aparência.',
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
    },
    fr: {
        navHome: 'Accueil',
        navEvents: 'Événements',
        navTourism: 'Tourisme',
        navPlaces: 'Lieux',
        navLogin: 'Se connecter',
        navSettings: 'Paramètres',
        navSupport: 'Assistance',
        navLogout: 'Se déconnecter',
        searchPlaceholder: 'Rechercher des événements',
        homeScroll: 'Faites glisser pour voir plus',
        homeInfoButton: 'Infos',
        homeInfoTitle: 'Qu’est-ce que SIGTUR ?',
        homeInfoText1: 'SIGTUR est un guide local de Salto conçu pour vous aider à découvrir les événements, lieux et expériences uniques de la ville.',
        homeInfoText2: 'La plateforme rassemble des recommandations culturelles, touristiques et de loisirs pour que chaque visite soit plus simple, mieux informée et plus mémorable.',
        homeFeaturesTitle: 'Événements à la une',
        homeFeatureEvents: 'Événements',
        homeFeatureTourism: 'Tourisme',
        homeFeaturePlaces: 'Lieux',
        homeFeatureEventsText1: 'Découvrez les dates et activités à ne pas manquer pour préparer votre visite à Salto.',
        homeFeatureEventsText2: 'Explorez des propositions pour chaque style et ne manquez pas ce que la ville a à offrir.',
        homeFeatureTourismText1: 'Trouvez des idées de parcours et des propositions pour profiter de la ville et de ses environs.',
        homeFeatureTourismText2: 'Découvrez des expériences, activités et conseils pour votre voyage.',
        homeFeaturePlacesText1: 'Découvrez les endroits les plus emblématiques et incontournables de Salto.',
        homeFeaturePlacesText2: 'Découvrez des lieux uniques où vivre des moments spéciaux.',
        homeEventsTitle: 'Événements à la une',
        homeEventButton: 'Voir plus',
        homeNewsTitle: 'Actualités à Salto',
        homeNewsButton: 'Lire la nouvelle',
        footerEvents: 'ÉVÉNEMENTS',
        footerPlaces: 'LIEUX',
        footerTourism: 'TOURISME',
        footerSigtur: 'SIGTUR',
        footerEventLinks: ['Événements en vedette', 'Événements à venir', 'Événements passés', 'Tous les événements'],
        footerPlaceLinks: ['Place Artigas', 'Place Treinta y Tres Orientales', 'Costanera Norte', 'Costanera Sud', 'Parc Benito Solari'],
        footerTourismLinks: ['Hôtels', 'Activités', 'Lieux touristiques', 'Expériences'],
        footerLegal: 'SIGTUR © 2026 par Bapst est sous licence CC BY-NC-ND 4.0',
        configBack: '← Retour',
        configEyebrow: '⚙️ Paramètres',
        configTitle: 'Personnalisez l’expérience de votre site',
        configDescription: 'Voici une base visuelle et structurelle pour gérer les données du site, les préférences utilisateur et les options d’apparence.',
        configGeneralTitle: 'Données générales',
        configGeneralText: 'Définissez l’identité et les informations principales de la page.',
        configLabelSiteName: 'Nom du site',
        configLabelEmail: 'Courriel de contact',
        configLabelCity: 'Ville',
        configVisualTitle: 'Préférences d’affichage',
        configVisualText: 'Contrôlez la façon dont les informations sont présentées à l’utilisateur.',
        configLabelTheme: 'Thème',
        configLabelLanguage: 'Langue',
        configLabelDescription: 'Brève description',
        configNotificationsTitle: 'Notifications',
        configNotificationsText: 'Activez ou désactivez les alertes importantes du projet.',
        configNotificationEventsTitle: 'Événements à la une',
        configNotificationEventsText: 'Recevez des alertes sur les activités nouvelles ou à venir.',
        configNotificationRemindersTitle: 'Rappels',
        configNotificationRemindersText: 'Envoi d’avis de mise à jour ou de maintenance.',
        configSecurityTitle: 'Sécurité et accès',
        configSecurityText: 'Gérez les accès et le contrôle de la plateforme.',
        configAccessPublicTitle: 'Accès public',
        configAccessPublicText: 'Permettre de voir le contenu sans se connecter.',
        configAdminPanelTitle: 'Panneau d’administration',
        configAdminPanelText: 'Accès privé pour modifier le contenu et les paramètres.',
        configSave: 'Enregistrer les changements',
        configReset: 'Réinitialiser',
        themeDark: 'Sombre',
        themeLight: 'Clair',
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
        eventsHeroLabel: 'ÉVÉNEMENTS',
        eventsHeroTitle: 'ÉVÉNEMENTS',
        eventsHeroText: 'Cette section réunit les différents événements touristiques, sportifs et culturels de Salto afin que vous puissiez vous informer et participer à des activités amusantes.',
        eventsFeaturedLabel: 'À la une',
        tourismHeroLabel: 'Découvrez le tourisme à Salto',
        tourismHeroTitle: 'TOURISME',
        tourismHeroText: 'Découvrez des propositions de tourisme local, des itinéraires gastronomiques et des expériences en plein air conçues pour chaque type de visiteur.',
        placesHeroLabel: 'Lieux historiques',
        placesHeroTitle: 'LIEUX',
        placesHeroText: 'Découvrez les lieux les plus emblématiques de Salto, riches en histoire et en culture.'
    },
    it: {
        navHome: 'Home',
        navEvents: 'Eventi',
        navTourism: 'Turismo',
        navPlaces: 'Luoghi',
        navLogin: 'Accedi',
        navSettings: 'Impostazioni',
        navSupport: 'Supporto',
        navLogout: 'Esci',
        searchPlaceholder: 'Cerca eventi',
        homeScroll: 'Scorri per vedere di più',
        homeInfoButton: 'Informazioni',
        homeInfoTitle: 'Cos’è SIGTUR?',
        homeInfoText1: 'SIGTUR è una guida locale di Salto pensata per aiutarti a scoprire eventi, luoghi ed esperienze uniche della città.',
        homeInfoText2: 'La piattaforma riunisce raccomandazioni culturali, turistiche e di svago, così ogni visita diventa più semplice, informata e memorabile.',
        homeFeaturesTitle: 'Eventi in evidenza',
        homeFeatureEvents: 'Eventi',
        homeFeatureTourism: 'Turismo',
        homeFeaturePlaces: 'Luoghi',
        homeFeatureEventsText1: 'Scopri le date e le attività in evidenza per programmare la tua visita a Salto.',
        homeFeatureEventsText2: 'Esplora proposte per ogni stile e non perdere il meglio che la città ha da offrire.',
        homeFeatureTourismText1: 'Trova idee di itinerari e proposte per goderti la città e i dintorni.',
        homeFeatureTourismText2: 'Connettiti con esperienze, attività e consigli per il tuo viaggio.',
        homeFeaturePlacesText1: 'Scopri i punti più emblematici e i luoghi imperdibili di Salto.',
        homeFeaturePlacesText2: 'Scopri luoghi unici dove vivere momenti speciali e ricordare la visita.',
        homeEventsTitle: 'Eventi in evidenza',
        homeEventButton: 'Vedi di più',
        homeNewsTitle: 'Notizie a Salto',
        homeNewsButton: 'Leggi la notizia',
        footerEvents: 'EVENTI',
        footerPlaces: 'LUOGHI',
        footerTourism: 'TURISMO',
        footerSigtur: 'SIGTUR',
        footerEventLinks: ['Eventi in evidenza', 'Prossimi eventi', 'Eventi passati', 'Tutti gli eventi'],
        footerPlaceLinks: ['Piazza Artigas', 'Piazza Treinta y Tres Orientales', 'Costanera Norte', 'Costanera Sud', 'Parco Benito Solari'],
        footerTourismLinks: ['Hotel', 'Attività', 'Luoghi turistici', 'Esperienze'],
        footerLegal: 'SIGTUR © 2026 di Bapst è concesso in licenza CC BY-NC-ND 4.0',
        configBack: '← Indietro',
        configEyebrow: '⚙️ Impostazioni',
        configTitle: 'Personalizza l’esperienza del tuo sito',
        configDescription: 'Qui trovi una base visiva e strutturale per gestire i dati del sito, le preferenze utente e le opzioni di aspetto.',
        configGeneralTitle: 'Dati generali',
        configGeneralText: 'Definisci l’identità e le informazioni principali della pagina.',
        configLabelSiteName: 'Nome del sito',
        configLabelEmail: 'Email di contatto',
        configLabelCity: 'Città',
        configVisualTitle: 'Preferenze di visualizzazione',
        configVisualText: 'Controlla il modo in cui le informazioni vengono presentate all’utente.',
        configLabelTheme: 'Tema',
        configLabelLanguage: 'Lingua',
        configLabelDescription: 'Descrizione breve',
        configNotificationsTitle: 'Notifiche',
        configNotificationsText: 'Attiva o disattiva gli avvisi importanti del progetto.',
        configNotificationEventsTitle: 'Eventi in evidenza',
        configNotificationEventsText: 'Ricevi avvisi su attività nuove o imminenti.',
        configNotificationRemindersTitle: 'Promemoria',
        configNotificationRemindersText: 'Invio di avvisi di aggiornamento o manutenzione.',
        configSecurityTitle: 'Sicurezza e accesso',
        configSecurityText: 'Gestisci gli accessi e il controllo della piattaforma.',
        configAccessPublicTitle: 'Accesso pubblico',
        configAccessPublicText: 'Consenti la visualizzazione dei contenuti senza effettuare il login.',
        configAdminPanelTitle: 'Pannello di amministrazione',
        configAdminPanelText: 'Accesso privato per modificare contenuti e impostazioni.',
        configSave: 'Salva modifiche',
        configReset: 'Ripristina',
        themeDark: 'Scuro',
        themeLight: 'Chiaro',
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
        eventsHeroLabel: 'EVENTI',
        eventsHeroTitle: 'EVENTI',
        eventsHeroText: 'Questa sezione riunisce i diversi eventi turistici, sportivi e culturali di Salto così puoi restare informato e partecipare ad attività divertenti.',
        eventsFeaturedLabel: 'In evidenza',
        tourismHeroLabel: 'Scopri il turismo a Salto',
        tourismHeroTitle: 'TURISMO',
        tourismHeroText: 'Esplora proposte di turismo locale, itinerari gastronomici ed esperienze all’aria aperta progettate per ogni tipo di visitatore.',
        placesHeroLabel: 'Luoghi storici',
        placesHeroTitle: 'LUOGHI',
        placesHeroText: 'Scopri i luoghi più emblematici di Salto, pieni di storia e cultura.'
    },
    de: {
        navHome: 'Startseite',
        navEvents: 'Veranstaltungen',
        navTourism: 'Tourismus',
        navPlaces: 'Orte',
        navLogin: 'Anmelden',
        navSettings: 'Einstellungen',
        navSupport: 'Support',
        navLogout: 'Abmelden',
        searchPlaceholder: 'Veranstaltungen suchen',
        homeScroll: 'Wischen, um mehr zu sehen',
        homeInfoButton: 'Info',
        homeInfoTitle: 'Was ist SIGTUR?',
        homeInfoText1: 'SIGTUR ist ein lokaler Guide für Salto, der Ihnen hilft, einzigartige Veranstaltungen, Orte und Erlebnisse der Stadt zu entdecken.',
        homeInfoText2: 'Die Plattform vereint kulturelle, touristische und Freizeitempfehlungen, damit jeder Besuch einfacher, informierter und unvergesslicher wird.',
        homeFeaturesTitle: 'Besondere Veranstaltungen',
        homeFeatureEvents: 'Veranstaltungen',
        homeFeatureTourism: 'Tourismus',
        homeFeaturePlaces: 'Orte',
        homeFeatureEventsText1: 'Entdecken Sie die herausragenden Daten und Aktivitäten, um Ihren Besuch in Salto zu planen.',
        homeFeatureEventsText2: 'Entdecken Sie Vorschläge für jeden Stil und verpassen Sie nicht, was die Stadt zu bieten hat.',
        homeFeatureTourismText1: 'Finden Sie Routenideen und Vorschläge, um die Stadt und ihre Umgebung zu genießen.',
        homeFeatureTourismText2: 'Erkunden Sie Erlebnisse, Aktivitäten und Tipps für Ihre Reise.',
        homeFeaturePlacesText1: 'Entdecken Sie die emblematischsten Orte und die Sehenswürdigkeiten von Salto.',
        homeFeaturePlacesText2: 'Entdecken Sie einzigartige Orte, an denen Sie besondere Momente erleben können.',
        homeEventsTitle: 'Besondere Veranstaltungen',
        homeEventButton: 'Mehr sehen',
        homeNewsTitle: 'Nachrichten aus Salto',
        homeNewsButton: 'Nachricht lesen',
        footerEvents: 'VERANSTALTUNGEN',
        footerPlaces: 'ORTE',
        footerTourism: 'TOURISMUS',
        footerSigtur: 'SIGTUR',
        footerEventLinks: ['Besondere Veranstaltungen', 'Kommende Veranstaltungen', 'Vergangene Veranstaltungen', 'Alle Veranstaltungen'],
        footerPlaceLinks: ['Plaza Artigas', 'Plaza Treinta y Tres Orientales', 'Costanera Norte', 'Costanera Sur', 'Parque Benito Solari'],
        footerTourismLinks: ['Hotels', 'Aktivitäten', 'Sehenswürdigkeiten', 'Erlebnisse'],
        footerLegal: 'SIGTUR © 2026 von Bapst ist lizenziert unter CC BY-NC-ND 4.0',
        configBack: '← Zurück',
        configEyebrow: '⚙️ Einstellungen',
        configTitle: 'Passen Sie das Erlebnis Ihrer Website an',
        configDescription: 'Hier finden Sie eine visuelle und strukturelle Basis, um Website-Daten, Benutzerpräferenzen und Erscheinungsoptionen zu verwalten.',
        configGeneralTitle: 'Allgemeine Daten',
        configGeneralText: 'Definieren Sie die Identität und die wichtigsten Informationen der Seite.',
        configLabelSiteName: 'Name der Website',
        configLabelEmail: 'Kontakt-E-Mail',
        configLabelCity: 'Stadt',
        configVisualTitle: 'Anzeigepräferenzen',
        configVisualText: 'Steuern Sie, wie Informationen dem Benutzer präsentiert werden.',
        configLabelTheme: 'Thema',
        configLabelLanguage: 'Sprache',
        configLabelDescription: 'Kurzbeschreibung',
        configNotificationsTitle: 'Benachrichtigungen',
        configNotificationsText: 'Aktivieren oder deaktivieren Sie wichtige Projektbenachrichtigungen.',
        configNotificationEventsTitle: 'Besondere Veranstaltungen',
        configNotificationEventsText: 'Erhalten Sie Warnungen zu neuen oder bevorstehenden Aktivitäten.',
        configNotificationRemindersTitle: 'Erinnerungen',
        configNotificationRemindersText: 'Versand von Aktualisierungs- oder Wartungsbenachrichtigungen.',
        configSecurityTitle: 'Sicherheit und Zugriff',
        configSecurityText: 'Verwalten Sie den Zugriff und die Steuerung der Plattform.',
        configAccessPublicTitle: 'Öffentlicher Zugang',
        configAccessPublicText: 'Erlauben Sie das Anzeigen von Inhalten ohne Anmeldung.',
        configAdminPanelTitle: 'Administrationsbereich',
        configAdminPanelText: 'Privater Zugriff zum Bearbeiten von Inhalten und Einstellungen.',
        configSave: 'Änderungen speichern',
        configReset: 'Zurücksetzen',
        themeDark: 'Dunkel',
        themeLight: 'Hell',
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
        eventsHeroLabel: 'VERANSTALTUNGEN',
        eventsHeroTitle: 'VERANSTALTUNGEN',
        eventsHeroText: 'Dieser Abschnitt vereint die verschiedenen touristischen, sportlichen und kulturellen Veranstaltungen von Salto, damit Sie sich informieren und an spannenden Aktivitäten teilnehmen können.',
        eventsFeaturedLabel: 'Im Fokus',
        tourismHeroLabel: 'Entdecken Sie den Tourismus in Salto',
        tourismHeroTitle: 'TOURISMUS',
        tourismHeroText: 'Erkunden Sie lokale Tourismusangebote, gastronomische Routen und Outdoor-Erlebnisse, die für jede Art von Besucher geeignet sind.',
        placesHeroLabel: 'Historische Orte',
        placesHeroTitle: 'ORTE',
        placesHeroText: 'Entdecken Sie die emblematischsten Orte von Salto, voller Geschichte und Kultur.'
    },
    ja: {
        navHome: 'ホーム',
        navEvents: 'イベント',
        navTourism: '観光',
        navPlaces: 'スポット',
        navLogin: 'ログイン',
        navSettings: '設定',
        navSupport: 'サポート',
        navLogout: 'ログアウト',
        searchPlaceholder: 'イベントを検索',
        homeScroll: 'スワイプしてもっと見る',
        homeInfoButton: '情報',
        homeInfoTitle: 'SIGTURとは？',
        homeInfoText1: 'SIGTURは、サルトのユニークなイベント、スポット、体験を発見するために設計された地元ガイドです。',
        homeInfoText2: 'このプラットフォームでは、文化・観光・レジャーのおすすめをまとめており、訪問がより簡単で、より情報豊富で、より思い出深いものになります。',
        homeFeaturesTitle: '注目イベント',
        homeFeatureEvents: 'イベント',
        homeFeatureTourism: '観光',
        homeFeaturePlaces: 'スポット',
        homeFeatureEventsText1: 'サルト訪問を計画するための注目の日程と活動を見つけましょう。',
        homeFeatureEventsText2: 'あらゆるスタイルに合う提案を探し、街の魅力を見逃さないでください。',
        homeFeatureTourismText1: '街やその周辺を楽しむためのルート案や提案を見つけましょう。',
        homeFeatureTourismText2: '旅行のための体験、アクティビティ、ヒントを見つけましょう。',
        homeEventsTitle: '注目イベント',
        homeEventButton: 'もっと見る',
        homeNewsTitle: 'サルトのニュース',
        homeNewsButton: 'ニュースを見る',
        footerEvents: 'イベント',
        footerPlaces: 'スポット',
        footerTourism: '観光',
        footerSigtur: 'SIGTUR',
        footerEventLinks: ['注目イベント', '今後のイベント', '過去のイベント', 'すべてのイベント'],
        footerPlaceLinks: ['プラザ・アルティガス', 'プラザ・トレインタ・イ・トレス', 'コスタネラ・ノルテ', 'コスタネラ・スール', 'パルケ・ベニート・ソラリ'],
        footerTourismLinks: ['ホテル', 'アクティビティ', '観光スポット', '体験'],
        footerLegal: 'SIGTUR © 2026 Bapst 所属、CC BY-NC-ND 4.0 ライセンス',
        configBack: '← 戻る',
        configEyebrow: '⚙️ 設定',
        configTitle: 'サイト体験をカスタマイズ',
        configDescription: 'ここでは、サイトデータ、ユーザー設定、見た目のオプションを管理するためのビジュアルと構造の基盤を確認できます。',
        configGeneralTitle: '基本情報',
        configGeneralText: 'ページのアイデンティティと主要情報を定義します。',
        configLabelSiteName: 'サイト名',
        configLabelEmail: '連絡先メール',
        configLabelCity: '都市',
        configVisualTitle: '表示設定',
        configVisualText: 'ユーザーに情報をどのように表示するかをコントロールします。',
        configLabelTheme: 'テーマ',
        configLabelLanguage: '言語',
        configLabelDescription: '短い説明',
        configNotificationsTitle: '通知',
        configNotificationsText: 'プロジェクトの重要な通知を有効または無効にします。',
        configNotificationEventsTitle: '注目イベント',
        configNotificationEventsText: '新しいイベントや近いイベントのアラートを受け取ります。',
        configNotificationRemindersTitle: 'リマインダー',
        configNotificationRemindersText: '更新やメンテナンスのお知らせを送信します。',
        configSecurityTitle: 'セキュリティとアクセス',
        configSecurityText: 'プラットフォームのアクセスと管理を行います。',
        configAccessPublicTitle: '公開アクセス',
        configAccessPublicText: 'ログインせずにコンテンツを閲覧できるようにします。',
        configAdminPanelTitle: '管理パネル',
        configAdminPanelText: 'コンテンツや設定を編集するための非公開アクセスです。',
        configSave: '変更を保存',
        configReset: 'リセット',
        themeDark: '暗い',
        themeLight: '明るい',
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
        eventsHeroLabel: 'イベント',
        eventsHeroTitle: 'イベント',
        eventsHeroText: 'このセクションでは、サルトのさまざまな観光・スポーツ・文化イベントをまとめており、楽しみながら参加できるようになっています。',
        eventsFeaturedLabel: '注目',
        tourismHeroLabel: 'サルトの観光を発見',
        tourismHeroTitle: '観光',
        tourismHeroText: 'あらゆるタイプの訪問者向けに設計された、地域の観光提案、グルメルート、屋外体験をご覧ください。',
        placesHeroLabel: '歴史的な場所',
        placesHeroTitle: 'スポット',
        placesHeroText: '歴史と文化に満ちたサルトで最も象徴的な場所を発見してください。'
    },
    zh: {
        navHome: '首页',
        navEvents: '活动',
        navTourism: '旅游',
        navPlaces: '地点',
        navLogin: '登录',
        navSettings: '设置',
        navSupport: '支持',
        navLogout: '退出',
        searchPlaceholder: '搜索活动',
        homeScroll: '滑动查看更多',
        homeInfoButton: '信息',
        homeInfoTitle: '什么是 SIGTUR？',
        homeInfoText1: 'SIGTUR 是为萨尔托设计的本地指南，帮助您发现这座城市独特的活动、地点和体验。',
        homeInfoText2: '该平台汇总了文化、旅游和休闲建议，让每次访问都变得更简单、更有信息量和更难忘。',
        homeFeaturesTitle: '精选活动',
        homeFeatureEvents: '活动',
        homeFeatureTourism: '旅游',
        homeFeaturePlaces: '地点',
        homeFeatureEventsText1: '发现适合规划您萨尔托之行的亮点日期和活动。',
        homeFeatureEventsText2: '探索适合各类风格的建议，不要错过这座城市提供的精彩内容。',
        homeFeatureTourismText1: '找到适合享受城市及其周边环境的路线建议。',
        homeFeatureTourismText2: '连接旅行中的体验、活动和建议。',
        homeFeaturePlacesText1: '了解萨尔托最具代表性的地标和必去之处。',
        homeFeaturePlacesText2: '发现独特的地点，在这里创造特别的回忆。',
        homeEventsTitle: '精选活动',
        homeEventButton: '查看更多',
        homeNewsTitle: '萨尔托新闻',
        homeNewsButton: '阅读新闻',
        footerEvents: '活动',
        footerPlaces: '地点',
        footerTourism: '旅游',
        footerSigtur: 'SIGTUR',
        footerEventLinks: ['精选活动', '即将举行的活动', '过去的活动', '所有活动'],
        footerPlaceLinks: ['阿蒂加斯广场', '特雷因塔与三位一体广场', '北岸', '南岸', '贝尼托·索拉里公园'],
        footerTourismLinks: ['酒店', '活动', '旅游景点', '体验'],
        footerLegal: 'SIGTUR © 2026 by Bapst 采用 CC BY-NC-ND 4.0 许可',
        configBack: '← 返回',
        configEyebrow: '⚙️ 设置',
        configTitle: '个性化您的网站体验',
        configDescription: '这里提供了一个视觉和结构化基础，用于管理网站数据、用户偏好和外观选项。',
        configGeneralTitle: '基本信息',
        configGeneralText: '定义页面的身份和主要信息。',
        configLabelSiteName: '网站名称',
        configLabelEmail: '联系邮箱',
        configLabelCity: '城市',
        configVisualTitle: '显示偏好',
        configVisualText: '控制向用户展示信息的方式。',
        configLabelTheme: '主题',
        configLabelLanguage: '语言',
        configLabelDescription: '简短描述',
        configNotificationsTitle: '通知',
        configNotificationsText: '启用或禁用项目的重要提醒。',
        configNotificationEventsTitle: '精选活动',
        configNotificationEventsText: '接收有关新活动或即将到来的活动的提醒。',
        configNotificationRemindersTitle: '提醒',
        configNotificationRemindersText: '发送更新或维护通知。',
        configSecurityTitle: '安全与访问',
        configSecurityText: '管理平台的访问和控制。',
        configAccessPublicTitle: '公开访问',
        configAccessPublicText: '允许在不登录的情况下查看内容。',
        configAdminPanelTitle: '管理面板',
        configAdminPanelText: '用于编辑内容和设置的私有访问。',
        configSave: '保存更改',
        configReset: '重置',
        themeDark: '深色',
        themeLight: '浅色',
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
        eventsHeroLabel: '活动',
        eventsHeroTitle: '活动',
        eventsHeroText: '本部分汇总了萨尔托的各种旅游、体育和文化活动，方便您了解信息并参加有趣的活动。',
        eventsFeaturedLabel: '精选',
        tourismHeroLabel: '发现萨尔托的旅游',
        tourismHeroTitle: '旅游',
        tourismHeroText: '探索为各类游客设计的当地旅游提案、美食路线和户外体验。',
        placesHeroLabel: '历史地点',
        placesHeroTitle: '地点',
        placesHeroText: '发现萨尔托最具代表性的地方，充满历史与文化。'
    },
    ar: {
        navHome: 'الرئيسية',
        navEvents: 'الفعاليات',
        navTourism: 'السياحة',
        navPlaces: 'الأماكن',
        navLogin: 'تسجيل الدخول',
        navSettings: 'الإعدادات',
        navSupport: 'الدعم',
        navLogout: 'تسجيل الخروج',
        searchPlaceholder: 'ابحث عن أحداث',
        homeScroll: 'اسحب لعرض المزيد',
        homeInfoButton: 'معلومة',
        homeInfoTitle: 'ما هو SIGTUR؟',
        homeInfoText1: 'SIGTUR هو دليل محلي لمدينة سالتو مصمم لمساعدتك في اكتشاف الفعاليات والأماكن والتجارب الفريدة في المدينة.',
        homeInfoText2: 'تجمع المنصة بين التوصيات الثقافية والسياحية والترفيهية حتى تصبح كل زيارة أبسط وأكثر اطلاعًا ولا تُنسى.',
        homeFeaturesTitle: 'الفعاليات المميزة',
        homeFeatureEvents: 'الفعاليات',
        homeFeatureTourism: 'السياحة',
        homeFeaturePlaces: 'الأماكن',
        homeFeatureEventsText1: 'اكتشف المواعيد والأنشطة المميزة لتخطيط زيارتك إلى سالتو.',
        homeFeatureEventsText2: 'استكشف المقترحات لكل أسلوب ولا تفوّت ما تقدمه المدينة.',
        homeFeatureTourismText1: 'اعثر على أفكار للمسارات المقترحة للاستمتاع بالمدينة ومحيطها.',
        homeFeatureTourismText2: 'تواصل مع التجارب والأنشطة والنصائح لرحلتك.',
        homeFeaturePlacesText1: 'اكتشف أهم النقاط المعبرة وأماكن لا بد من زيارتها في سالتو.',
        homeFeaturePlacesText2: 'اكتشف أماكن فريدة حيث يمكنك خلق ذكريات خاصة.',
        homeEventsTitle: 'الفعاليات المميزة',
        homeEventButton: 'عرض المزيد',
        homeNewsTitle: 'أخبار سالتو',
        homeNewsButton: 'اقرأ الخبر',
        footerEvents: 'الفعاليات',
        footerPlaces: 'الأماكن',
        footerTourism: 'السياحة',
        footerSigtur: 'SIGTUR',
        footerEventLinks: ['الفعاليات البارزة', 'الفعاليات القادمة', 'الفعاليات السابقة', 'جميع الفعاليات'],
        footerPlaceLinks: ['ساحة أرتيغاس', 'ساحة ترينتا ي تريسه أوريانتاليس', 'كوستانيرا نورتي', 'كوستانيرا سور', 'حديقة بينيتو سولاري'],
        footerTourismLinks: ['فنادق', 'أنشطة', 'أماكن سياحية', 'تجارب'],
        footerLegal: 'SIGTUR © 2026 بواسطة Bapst مرخص بموجب CC BY-NC-ND 4.0',
        configBack: '← العودة',
        configEyebrow: '⚙️ الإعدادات',
        configTitle: 'خصص تجربة موقعك',
        configDescription: 'إليك أساس بصري وهيكلي لإدارة بيانات الموقع وتفضيلات المستخدم وخيارات المظهر.',
        configGeneralTitle: 'البيانات العامة',
        configGeneralText: 'حدد هوية الصفحة ومعلوماتها الأساسية.',
        configLabelSiteName: 'اسم الموقع',
        configLabelEmail: 'بريد التواصل',
        configLabelCity: 'المدينة',
        configVisualTitle: 'تفضيلات العرض',
        configVisualText: 'تحكم في طريقة عرض المعلومات للمستخدم.',
        configLabelTheme: 'السمة',
        configLabelLanguage: 'اللغة',
        configLabelDescription: 'وصف مختصر',
        configNotificationsTitle: 'الإشعارات',
        configNotificationsText: 'قم بتفعيل أو إلغاء تنشيط التنبيهات المهمة للمشروع.',
        configNotificationEventsTitle: 'الفعاليات المميزة',
        configNotificationEventsText: 'استقبل تنبيهات بشأن أنشطة جديدة أو قادمة.',
        configNotificationRemindersTitle: 'التذكيرات',
        configNotificationRemindersText: 'إرسال إشعارات التحديث أو الصيانة.',
        configSecurityTitle: 'الأمان والوصول',
        configSecurityText: 'إدارة وصول المنصة والتحكم فيها.',
        configAccessPublicTitle: 'الوصول العام',
        configAccessPublicText: 'السماح بعرض المحتوى دون تسجيل الدخول.',
        configAdminPanelTitle: 'لوحة الإدارة',
        configAdminPanelText: 'وصول خاص لتعديل المحتوى والإعدادات.',
        configSave: 'حفظ التغييرات',
        configReset: 'إعادة الضبط',
        themeDark: 'داكن',
        themeLight: 'فاتح',
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
        eventsHeroLabel: 'الفعاليات',
        eventsHeroTitle: 'الفعاليات',
        eventsHeroText: 'تجمع هذه الأقسام مختلف الفعاليات السياحية والرياضية والثقافية في سالتو حتى تتمكن من البقاء على اطلاع والمشاركة في أنشطة ممتعة.',
        eventsFeaturedLabel: 'مميز',
        tourismHeroLabel: 'اكتشف السياحة في سالتو',
        tourismHeroTitle: 'السياحة',
        tourismHeroText: 'استكشف عروض السياحة المحلية والممرات الغذائية والتجارب الخارجية المصممة لكل نوع من الزوار.',
        placesHeroLabel: 'أماكن تاريخية',
        placesHeroTitle: 'الأماكن',
        placesHeroText: 'اكتشف أكثر الأماكن رمزية في سالتو، المليئة بالتاريخ والثقافة.'
    },
    ru: {
        navHome: 'Главная',
        navEvents: 'События',
        navTourism: 'Туризм',
        navPlaces: 'Места',
        navLogin: 'Войти',
        navSettings: 'Настройки',
        navSupport: 'Поддержка',
        navLogout: 'Выйти',
        searchPlaceholder: 'Искать события',
        homeScroll: 'Проведите, чтобы увидеть больше',
        homeInfoButton: 'Инфо',
        homeInfoTitle: 'Что такое SIGTUR?',
        homeInfoText1: 'SIGTUR — это локальный гид по Сальто, созданный, чтобы помочь вам открыть для себя уникальные события, места и впечатления города.',
        homeInfoText2: 'Платформа объединяет культурные, туристические и развлекательные рекомендации, чтобы каждое посещение было проще, информативнее и запоминающися.',
        homeFeaturesTitle: 'Избранные события',
        homeFeatureEvents: 'События',
        homeFeatureTourism: 'Туризм',
        homeFeaturePlaces: 'Места',
        homeFeatureEventsText1: 'Откройте для себя важные даты и мероприятия для планирования поездки в Сальто.',
        homeFeatureEventsText2: 'Исследуйте предложения для любого стиля и не пропустите лучшее, что может предложить город.',
        homeFeatureTourismText1: 'Найдите идеи маршрутов и предложений, чтобы насладиться городом и его окрестностями.',
        homeFeatureTourismText2: 'Подключайтесь к впечатлениям, активностям и советам для вашей поездки.',
        homeFeaturePlacesText1: 'Откройте для себя самые знаковые точки и обязательные места Сальто.',
        homeFeaturePlacesText2: 'Откройте для себя уникальные места, где можно создать особые воспоминания.',
        homeEventsTitle: 'Избранные события',
        homeEventButton: 'Смотреть больше',
        homeNewsTitle: 'Новости Сальто',
        homeNewsButton: 'Читать новость',
        footerEvents: 'СОБЫТИЯ',
        footerPlaces: 'МЕСТА',
        footerTourism: 'ТУРИЗМ',
        footerSigtur: 'SIGTUR',
        footerEventLinks: ['Избранные события', 'Предстоящие события', 'Прошедшие события', 'Все события'],
        footerPlaceLinks: ['Площадь Артигаса', 'Площадь Трехсот', 'Костанера Норте', 'Костанера Сур', 'Парк Бенито Солари'],
        footerTourismLinks: ['Отели', 'Активности', 'Туристические места', 'Впечатления'],
        footerLegal: 'SIGTUR © 2026 Bapst лицензирован по CC BY-NC-ND 4.0',
        configBack: '← Назад',
        configEyebrow: '⚙️ Настройки',
        configTitle: 'Настройте опыт вашего сайта',
        configDescription: 'Вот визуальная и структурная основа для управления данными сайта, пользовательскими настройками и параметрами внешнего вида.',
        configGeneralTitle: 'Общие данные',
        configGeneralText: 'Определите идентичность и основную информацию страницы.',
        configLabelSiteName: 'Название сайта',
        configLabelEmail: 'Контактный email',
        configLabelCity: 'Город',
        configVisualTitle: 'Настройки отображения',
        configVisualText: 'Контролируйте, как информация представляется пользователю.',
        configLabelTheme: 'Тема',
        configLabelLanguage: 'Язык',
        configLabelDescription: 'Краткое описание',
        configNotificationsTitle: 'Уведомления',
        configNotificationsText: 'Включайте или отключайте важные уведомления проекта.',
        configNotificationEventsTitle: 'Избранные события',
        configNotificationEventsText: 'Получайте оповещения о новых или предстоящих активностях.',
        configNotificationRemindersTitle: 'Напоминания',
        configNotificationRemindersText: 'Отправка уведомлений об обновлениях или обслуживании.',
        configSecurityTitle: 'Безопасность и доступ',
        configSecurityText: 'Управляйте доступом и контролем платформы.',
        configAccessPublicTitle: 'Открытый доступ',
        configAccessPublicText: 'Разрешить просматривать содержимое без входа в систему.',
        configAdminPanelTitle: 'Панель администрирования',
        configAdminPanelText: 'Частный доступ для редактирования контента и настроек.',
        configSave: 'Сохранить изменения',
        configReset: 'Сбросить',
        themeDark: 'Тёмная',
        themeLight: 'Светлая',
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
        eventsHeroLabel: 'СОБЫТИЯ',
        eventsHeroTitle: 'СОБЫТИЯ',
        eventsHeroText: 'Этот раздел объединяет различные туристические, спортивные и культурные события Сальто, чтобы вы могли быть в курсе и участвовать в увлекательных мероприятиях.',
        eventsFeaturedLabel: 'Избранное',
        tourismHeroLabel: 'Откройте туризм в Сальто',
        tourismHeroTitle: 'ТУРИЗМ',
        tourismHeroText: 'Изучайте местные туристические предложения, гастрономические маршруты и мероприятия на открытом воздухе, созданные для каждого типа посетителя.',
        placesHeroLabel: 'Исторические места',
        placesHeroTitle: 'МЕСТА',
        placesHeroText: 'Откройте для себя самые знаковые места Сальто, полные истории и культуры.'
    }
};

const traduccionesExtra = {
    es: {
        navProfile: 'Perfil',
        navMenuOpen: 'Abrir menú',
        navSearch: 'Buscar',
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
        supportContactWhatsapp: 'WhatsApp: +598 99 999 999',
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
        eventsReviewNames: ['Benjamín R.', 'Aaron C.', 'Santiago D.', 'Federico S.', 'Pío M.']
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
        tourismGalleryTitles: ['Parque Benito Solari', 'La Fosa', 'Shopping Salto', 'Cine Sarandi'],
        tourismGalleryAlt: ['Parque Solari', 'La Fosa', 'Shopping Salto', 'Cine Sarandi'],
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
        placesGalleryTitles: ['Plaza Treinta y Tres', 'Plaza Artigas', 'Parque Benito Solari', 'Termas del Dayman'],
        placesGalleryAlt: ['Vista de evento', 'Plaza central', 'Espacio de descanso', 'Punto turístico']
    },
    en: {
        navProfile: 'Profile',
        navMenuOpen: 'Open menu',
        navSearch: 'Search',
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
        supportContactWhatsapp: 'WhatsApp: +598 99 999 999',
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
        eventsReviewNames: ['Benjamin R.', 'Aaron C.', 'Santiago D.', 'Federico S.', 'Pio M.']
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
        tourismGalleryTitles: ['Parque Benito Solari', 'La Fosa', 'Salto Shopping', 'Cine Sarandi'],
        tourismGalleryAlt: ['Parque Solari', 'La Fosa', 'Salto shopping', 'Cine Sarandi'],
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
        placesGalleryTitles: ['Plaza Treinta y Tres', 'Plaza Artigas', 'Parque Benito Solari', 'Termas del Dayman'],
        placesGalleryAlt: ['Event view', 'Central square', 'Rest area', 'Tourist spot']
    }
};

Object.entries(traduccionesExtra).forEach(([idioma, valores]) => {
    if (traducciones[idioma]) {
        Object.assign(traducciones[idioma], valores);
    }
});

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

const aplicarIdioma = (idioma = leerIdioma()) => {
    const idiomaActivo = idiomasDisponibles.includes(idioma) ? idioma : 'es';
    guardarIdioma(idiomaActivo);
    document.documentElement.lang = idiomaActivo;
    document.documentElement.setAttribute('data-lang', idiomaActivo);
    document.body.dataset.lang = idiomaActivo;

    const traduccion = traducciones[idiomaActivo] || traducciones.es;

    document.querySelectorAll('[data-i18n]').forEach((elemento) => {
        const clave = elemento.getAttribute('data-i18n');
        const valor = traduccion[clave] ?? traducciones.es[clave] ?? traducciones.en[clave];
        if (!valor) return;
        if (elemento.tagName === 'INPUT' || elemento.tagName === 'TEXTAREA') {
            if (elemento.getAttribute('data-i18n-placeholder')) {
                elemento.placeholder = valor;
            }
            return;
        }
        if (Array.isArray(valor)) {
            const indice = parseInt(elemento.getAttribute('data-i18n-index') || '0', 10);
            elemento.textContent = valor[indice] || valor[0];
            return;
        }
        elemento.textContent = valor;
    });

    document.querySelectorAll('[data-i18n-placeholder]').forEach((elemento) => {
        const clave = elemento.getAttribute('data-i18n-placeholder');
        const valor = traduccion[clave] ?? traducciones.es[clave] ?? traducciones.en[clave];
        if (valor) elemento.placeholder = valor;
    });

    document.querySelectorAll('[data-i18n-title]').forEach((elemento) => {
        const clave = elemento.getAttribute('data-i18n-title');
        const valor = traduccion[clave] ?? traducciones.es[clave] ?? traducciones.en[clave];
        if (valor) {
            elemento.textContent = valor;
            if (elemento.tagName === 'TITLE') {
                document.title = valor;
            }
        }
    });

    document.querySelectorAll('[data-i18n-aria-label]').forEach((elemento) => {
        const clave = elemento.getAttribute('data-i18n-aria-label');
        const valor = traduccion[clave] ?? traducciones.es[clave] ?? traducciones.en[clave];
        if (valor) elemento.setAttribute('aria-label', valor);
    });

    document.querySelectorAll('[data-i18n-alt]').forEach((elemento) => {
        const clave = elemento.getAttribute('data-i18n-alt');
        const valor = traduccion[clave] ?? traducciones.es[clave] ?? traducciones.en[clave];
        if (valor) elemento.setAttribute('alt', valor);
    });

    document.querySelectorAll('option[data-i18n]').forEach((option) => {
        const clave = option.getAttribute('data-i18n');
        const valor = traduccion[clave] ?? traducciones.es[clave] ?? traducciones.en[clave];
        if (valor) option.textContent = valor;
    });

    const selectorIdioma = document.getElementById('idioma');
    if (selectorIdioma) {
        selectorIdioma.value = idiomaActivo;
    }
};

// chequea si el nodo esta dentro de la barra de navegacion o el perfil
const esNav = (elemento) => {
    return !!elemento.closest('header, nav, .menu, .bottom-nav, .perfil-menu');
};

// chequea si es el boton de informacion, para no tocarlo con el modo oscuro
const esInfo = (elemento) => {
    return elemento && elemento.classList.contains('info-btn');
};

const aplicarTema = (oscuro, botonModoOscuro) => {
    const colorTexto = oscuro ? '#ffffff' : ''; // si estamos en modo oscuro, el texto debe ser blanco
    const colorFondo = oscuro ? '#050505' : ''; // fondo negro mate para toda la pagina
    const fondoBoton = oscuro ? 'rgba(255,255,255,0.12)' : ''; // fondo tenue para botones menos importantes

    setStyle(body, 'backgroundColor', colorFondo); // aplico el fondo oscuro al body
    setStyle(body, 'backgroundImage', oscuro ? 'none' : ''); // saco imagen de fondo si hay una
    body.dataset.tema = oscuro ? 'oscuro' : 'claro'; // marca el tema actual para CSS

    if (menu) {
        setStyle(menu, 'backgroundColor', 'transparent'); // la nav siempre queda transparente, no la lleno de oscuro
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

const activarHamburguesa = () => {
    if (!hamburguesa || !menu) return;
    hamburguesa.addEventListener('click', () => {
        menu.classList.toggle('activo'); // abre/cierra menu movil
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

const cambiarSlide = (nuevoIndice) => {
    if (slides.length === 0 || indicadores.length === 0) return;
    slides[indiceActivo].classList.remove('activo');
    indicadores[indiceActivo].classList.remove('activo');
    indiceActivo = (nuevoIndice + slides.length) % slides.length; // Calcula índice con ciclo
    slides[indiceActivo].classList.add('activo'); // Activa nueva
    indicadores[indiceActivo].classList.add('activo');
};

const activarCarrusel = () => {
    if (flechaIzquierda) {
        flechaIzquierda.addEventListener('click', () => {
            cambiarSlide(indiceActivo - 1);
        });
    }
    if (flechaDerecha) {
        flechaDerecha.addEventListener('click', () => {
            cambiarSlide(indiceActivo + 1);
        });
    }
    indicadores.forEach((indicador, index) => {
        indicador.addEventListener('click', () => {
            cambiarSlide(index);
        });
    });
};

const activarToggleFeature = () => {
    const togglesFeature = document.querySelectorAll('.feature-toggle');
    togglesFeature.forEach((toggle) => {
        toggle.addEventListener('click', (evento) => {
            const boton = evento.currentTarget;
            const feature = boton.closest('.feature');
            const expandido = boton.getAttribute('aria-expanded') === 'true';
            const abrir = !expandido;
            if (!feature) return;
            feature.classList.toggle('activo', abrir);
            feature.classList.toggle('open', abrir);
            boton.classList.toggle('open', abrir);
            boton.setAttribute('aria-expanded', String(abrir));
        });
    });
};

const activarToggleFooter = () => {
    const togglesFooter = document.querySelectorAll('.footer-toggle');
    togglesFooter.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const footerCol = toggle.closest('.footer-col');
            const expandido = toggle.getAttribute('aria-expanded') === 'true';
            const abrir = !expandido;
            if (!footerCol) return;
            footerCol.classList.toggle('open', abrir);
            toggle.classList.toggle('open', abrir);
            toggle.setAttribute('aria-expanded', String(abrir));
        });
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
activarBotonUsuarioInferior();
cerrarClickAfuera();
activarCarrusel();
activarToggleFeature();
activarToggleFooter();
activarInfo();
