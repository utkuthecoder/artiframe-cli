<?php
return [
    'WELCOME'              => 'Bienvenue dans la CLI ArtiFrame !',
    'CORE_WARNING_TITLE'   => 'Avertissement du Noyau ArtiFrame !',
    'CORE_WARNING_BODY'    => 'Attention ! Ce répertoire et ces fichiers contiennent l\'architecture centrale d\'ArtiFrame.' . PHP_EOL . 'Les modifications apportées ici affecteront globalement le fonctionnement de l\'application,' . PHP_EOL . 'les couches de sécurité et les dépendances de l\'API.' . PHP_EOL . 'À moins que vous ne configuriez un comportement personnalisé ou un correctif de framework,' . PHP_EOL . 'il est recommandé de ne pas modifier les fichiers de ce répertoire.',
    'CONFIRM_PROMPT'       => 'Approuvez-vous ? [o/N] : ',
    'ABORTED'              => 'Opération annulée par l\'utilisateur.',
    'DIR_REQUIRED_ERROR'   => 'Erreur : Le répertoire de destination de la classe doit être spécifié ! (ex. /app ou /src)',
    'COMPOSER_MISSING_TITLE' => 'Composer introuvable !',
    'COMPOSER_MISSING_BODY'  => 'ArtiFrame nécessite Composer (gestionnaire de paquets PHP) pour créer des projets.' . PHP_EOL . PHP_EOL . 'Installation :' . PHP_EOL . '- Windows : Téléchargez https://getcomposer.org/Composer-Setup.exe' . PHP_EOL . '- macOS : Exécutez \'brew install composer\'' . PHP_EOL . '- Linux : Exécutez \'sudo apt install composer\'' . PHP_EOL . PHP_EOL . 'Veuillez redémarrer votre terminal après l\'installation. No composer, no project !',
    'INSTALL_SUCCESS'      => '[SUCCÈS] Installation terminée. Veuillez redémarrer votre terminal pour appliquer les nouvelles variables d\'environnement.',

    // NewProjectCommand
    'PROJECT_BUILDER_TITLE' => '🚀  Générateur de Projet ArtiFrame',
    'PROJECT_LABEL'         => '📁 Projet',
    'LOCATION_LABEL'        => '📍 Emplacement',
    'PHASE_COPYING'         => 'Copie des modèles de base...',
    'PHASE_DIRS'            => 'Création des répertoires de modules...',
    'PHASE_FILES'           => 'Génération des fichiers du projet...',
    'PHASE_DEPS'            => 'Installation des dépendances...',
    'PHASE_HEADER'          => 'Phase :current/:total —',
    'FILES_TO_PROCESS'      => ':count fichiers à traiter',
    'SUCCESS_TITLE'         => '✅  Projet créé avec succès !',
    'NEXT_STEPS'            => '🎯 Pour commencer :',
    'NEXT_STEPS_EDIT_ENV'   => 'Modifiez votre fichier .env et commencez à développer !',
    'DIR_ITEM_COUNT'        => '(:count éléments)',

    // MakeViewCommand
    'ERROR_VIEW_PATH_REQUIRED' => 'Erreur : Le chemin de la vue est requis (ex. dashboard.php ou /admin/utilisateurs/liste.php)',
    'ERROR_STUB_NOT_FOUND'     => 'Erreur : Fichier stub introuvable : :path',
    'ERROR_RUN_FROM_ROOT'      => 'Assurez-vous d\'exécuter cette commande depuis la racine d\'un projet ArtiFrame.',
    'SUCCESS_VIEW'             => '✅ Vue créée : /public/:path',
    'SUCCESS_CSS'              => '✅ CSS créé : /public:path',
    'SUCCESS_JS'               => '✅ JS créé :  /public:path',

    // MakeApiCommand
    'ERROR_API_TYPE'           => "Erreur : Le type d'API doit être 'standart' ou 'switch-case'.",
    'ERROR_API_PATH_REQUIRED'  => 'Erreur : Le chemin cible est requis (ex. /v1/auth/connexion.php).',
    'SUCCESS_API'              => '✅ Point de terminaison API créé : /public/api/:type/:path',

    // MakeClassCommand
    'ERROR_CLASS_ROOT'         => 'Erreur : La cible doit se trouver dans les répertoires /app/ ou /src/.',
    'SUCCESS_CLASS'            => '✅ Classe créée : /:path',
    'NAMESPACE_LABEL'          => '   Espace de noms (Namespace) :',

    // VersionCommand
    'ERROR_VERSION_ACTION'     => "Erreur : L'action doit être 'upgrade' ou 'downgrade'.",
    'ERROR_VERSION_LEVEL'      => "Erreur : Le niveau doit être 'major', 'minor' ou 'patch'.",
    'ERROR_VERSION_FILE'       => 'Erreur : app-version.php introuvable : :path',
    'ERROR_VERSION_PARSE'      => 'Erreur : Impossible de lire APP_VERSION dans app-version.php.',
    'WARN_VERSION_UNCHANGED'   => '⚠️  Avertissement : La version est déjà au minimum (:version) ou n\'a pas changé.',
    'SUCCESS_VERSION'          => '✅ Version mise à jour : :old → :new',

    // Shell UI
    'SHELL_TYPE_HELP'  => 'Tapez ' . "\033[38;2;0;157;108m" . 'help' . "\033[0m" . ' pour les commandes, ou ' . "\033[38;2;0;157;108m" . 'exit' . "\033[0m" . ' pour quitter.',
    'SHELL_GOODBYE'    => 'Au revoir. Construisez quelque chose de grand.',
    'SHELL_ERROR'      => 'Erreur :',

    // Help screen
    'HELP_COMMANDS'          => 'COMMANDES',
    'HELP_NEW_DESC'          => 'Crée un nouveau projet ArtiFrame de zéro.',
    'HELP_MAKEVIEW_DESC1'    => 'Génère un nouveau fichier vue avec ses assets CSS et JS.',
    'HELP_MAKEVIEW_DESC2'    => 'Le chemin peut utiliser des sous-répertoires (créés automatiquement).',
    'HELP_MAKEAPI_DESC'      => 'Génère un nouveau fichier de point de terminaison API.',
    'HELP_MAKEAPI_STANDART'  => 'Point de terminaison unique (une requête, une réponse).',
    'HELP_MAKEAPI_SWITCH'    => 'Point de terminaison multi-actions avec routage.',
    'HELP_MAKECLASS_DESC'    => 'Génère un nouveau fichier de classe PHP avec un espace de noms.',
    'HELP_VERSION_DESC1'     => 'Gère le numéro de version sémantique dans la configuration du projet.',
    'HELP_VERSION_FORMAT'    => 'Format de version : MAJOR.MINOR.PATCH  (ex. 2.4.1)',
    'HELP_PATCH_UP'          => 'Corrections de bogues, petites modifications.',
    'HELP_MINOR_UP'          => 'Nouvelle fonctionnalité rétrocompatible.',
    'HELP_MAJOR_UP'          => 'Changements non rétrocompatibles.',
    'HELP_PATCH_DOWN'        => 'Annuler le dernier correctif.',
    'HELP_MINOR_DOWN'        => 'Annuler la dernière version mineure.',
    'HELP_MAJOR_DOWN'        => 'Annuler la dernière version majeure.',
    'HELP_HELP_DESC'         => 'Afficher ce message d\'aide.',
    'HELP_EXIT_DESC'         => 'Quitter le shell interactif.',

    // LangCommand
    'LANG_CURRENT'        => 'Langue actuelle :',
    'LANG_SELECT'         => 'Entrez un numéro pour changer (0 pour annuler) :',
    'LANG_PROMPT'         => 'Votre choix [1-5] ou 0 :',
    'LANG_UNCHANGED'      => 'Langue inchangée.',
    'LANG_CHANGED'        => 'Langue modifiée : :old → :new',
    'LANG_ALREADY_SET'    => 'La langue est déjà définie sur :lang.',
    'LANG_RESTART_TIP'    => 'Le changement prendra effet dès la prochaine session.',
    'LANG_INVALID'        => 'Code de langue invalide : ":code".',
    'LANG_VALID_LIST'     => 'Langues prises en charge',
    'LANG_INVALID_CHOICE' => 'Choix invalide. Veuillez entrer un chiffre entre 1 et 5.',
    'HELP_LANG_DESC'      => 'Afficher et modifier la préférence de langue.',

    // CliVersionCommand
    'VERSION_CURRENT'          => 'Version actuelle',
    'VERSION_LATEST'           => 'Dernière version',
    'VERSION_CHECKING'         => 'Vérification des mises à jour...',
    'VERSION_NETWORK_ERROR'    => '⚠️   Impossible de vérifier les mises à jour (erreur réseau).',
    'VERSION_UP_TO_DATE'       => '✅  À jour — Aucune nouvelle version disponible.',
    'VERSION_UPDATE_AVAILABLE' => '🔔  Mise à jour disponible !',
    'VERSION_UPDATE_PROMPT'    => 'Mettre à jour maintenant ? [o/N] :',
    'VERSION_YES_KEYS'         => 'o,y',
    'VERSION_UPDATE_CANCELLED' => 'Mise à jour annulée.',
    'VERSION_UPDATING'         => '📦  Mise à jour en cours...',
    'VERSION_UPDATE_SUCCESS'   => '✅  Mise à jour terminée ! Veuillez redémarrer votre terminal.',
    'VERSION_UPDATE_FAILED'    => '❌  Échec de la mise à jour.',
    'HELP_CLI_VERSION_DESC'    => 'Afficher la version CLI et vérifier les mises à jour.',
];
