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
    'HELP_AUTH_DESC'         => 'Authentifiez-vous avec GitHub pour permettre les suggestions.',
    'HELP_SUGGEST_DESC'      => 'Soumettez une demande de fonctionnalité, un rapport de bogue ou une suggestion d\'aide directement sur GitHub.',

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

    // Terminal Launch
    'TERMINAL_OPENED'    => 'Nouveau terminal ouvert dans le répertoire du projet.',
    'TERMINAL_CLOSE_OLD' => 'Vous pouvez fermer cette fenêtre.',
    'TERMINAL_FALLBACK'  => 'Impossible d\'ouvrir le terminal automatiquement. Veuillez accéder au répertoire du projet :',

    // AddCommand
    'ADD_MISSING_NAME'    => 'Nom du paquet non spécifié. Utilisation : add <nom-du-paquet>',
    'ADD_NOT_FOUND'       => '":name" n\'a pas été trouvé dans le registre des paquets ArtiFrame.',
    'ADD_NO_PROJECT'      => 'Aucun projet ArtiFrame trouvé dans ce répertoire. (composer.json manquant)',
    'ADD_INSTALLING'      => 'Installation de :name... (:composer)',
    'ADD_FAILED'          => 'L\'installation de :name a échoué.',
    'ADD_SUCCESS'          => ':name installé avec succès ! (:composer)',
    'ADD_SERVICE_NEEDED'  => 'La classe de service doit être créée : :path',
    'ADD_LIST_TITLE'      => 'Paquets disponibles',
    'ADD_CAT_INTEGRATED'  => 'Intégré (Composer + Classe de service)',
    'ADD_CAT_DIRECT'      => 'Utilisation directe (Composer uniquement)',
    'HELP_ADD_DESC'       => 'Ajoute un paquet approuvé par ArtiFrame au projet.',
    'HELP_ADD_LIST'       => 'Liste tous les paquets',

    // AddCommand (Extended)
    'ADD_ALREADY'         => ':name est déjà installé dans ce projet.',
    'ADD_SERVICE_CREATED' => 'Classe de service créée : :path',
    'ADD_ENV_UPDATED'     => 'Fichiers .env et .env.example mis à jour.',
    'ADD_EDIT_ENV'        => 'Veuillez remplir les clés API dans votre fichier .env.',
    'ADD_STUB_MISSING'    => 'Modèle de service introuvable pour :name.',
    'ADD_SERVICE_EXISTS'  => 'Le fichier de service existe déjà : :path (ignoré)',

    // Show / List
    'SHOW_CURRENT_DIR'    => 'Répertoire de travail actuel :',
    'HELP_SHOW_DESC'      => 'Affiche le répertoire de travail actuel.',
    'HELP_LIST_DESC'      => 'Affiche l\'arborescence du répertoire actuel.',

    // Go
    'GO_MISSING_DIR'      => 'Veuillez spécifier le répertoire. (ex: go public ou go back)',
    'GO_NOT_FOUND'        => 'Le répertoire spécifié est introuvable : :dir',
    'GO_SUCCESS'          => 'Répertoire modifié avec succès.',
    'HELP_GO_DESC'        => 'Permet de changer de répertoire courant.',

    // Remove
    'REMOVE_TARGET_REQUIRED' => 'Veuillez spécifier le nom ou le chemin du fichier que vous souhaitez supprimer.',
    'REMOVE_FILE_NOT_FOUND'  => 'Le fichier spécifié est introuvable : :path',
    'REMOVE_CONFIRM_FILE'    => 'Vous êtes sur le point de supprimer DÉFINITIVEMENT ce fichier. Êtes-vous sûr ? :path',
    'REMOVE_CONFIRM_ASSETS'  => 'Les fichiers CSS et JS associés à cette Vue doivent-ils également être supprimés définitivement ?',
    'REMOVE_FOUND_APIS'      => 'ATTENTION : La classe :class supprimée est utilisée dans ces fichiers API :',
    'REMOVE_API_WARNING'     => 'Vous devez modifier ou supprimer manuellement les lignes concernées dans les fichiers API ci-dessus.',
    'REMOVE_SUCCESS'         => 'Fichier supprimé avec succès : :path',
    'HELP_REMOVE_DESC'       => 'Supprime un fichier (et ses dépendances si confirmé) en toute sécurité.',

    // Serve
    'SERVE_NOT_PROJECT' => 'Ce répertoire n\'est pas un projet ArtiFrame valide (public/index.php manquant).',
    'SERVE_STARTING'    => 'Démarrage du serveur de développement : :url',
    'SERVE_STOP_INFO'   => 'Appuyez sur CTRL+C pour arrêter le serveur.',
    'HELP_SERVE_DESC'   => 'Démarre le serveur de développement local.',

    'INVALID_PACKAGE_NAME' => 'Nom de package invalide. Le format doit ressembler à : vendor/project',

    'MODE_ALREADY_DEBUG'       => 'Le mode de débogage est déjà actif.',
    'MODE_ALREADY_PROD'        => 'Le mode de production est déjà actif.',
    'MODE_CHANGED'             => 'Passé du mode :old au mode :new.',
    'MODE_INVALID'             => 'Mode invalide. Utilisez 1 pour Débogage, 0 pour Production.',
    'MODE_NOT_PROJECT'         => 'Projet ArtiFrame introuvable (config/app-version.php manquant).',
    'HELP_MODE_DESC'           => 'Modifie le mode de l\'application (0=Prod, 1=Debug)',

    // Table
    'ERROR_TABLE_NAME_REQUIRED' => 'Le nom de la table est requis.',
    'ERROR_STUB_NOT_FOUND'      => 'Le fichier sql.stub est introuvable.',
    'ERROR_TABLE_NOT_IN_STUB'   => 'La définition de la table \'%s\' est introuvable dans le fichier stub.',
    'SUCCESS_TABLE_ADDED'       => 'Table \'%s\' ajoutée avec succès à schema.sql',
    'HELP_TABLE_DESC'           => 'Ajoute un schéma de table standard de sql.stub à schema.sql',
    'TABLE_LIST_HEADER' => 'Tables prises en charge :',
    'TABLE_DESC_USERS' => 'Table de base pour les utilisateurs et l\'autorisation (UUID).',
    'TABLE_DESC_USER_SESSIONS' => 'Suivi des sessions utilisateur (IP, appareil, token).',
    'TABLE_DESC_USER_PREFERENCES' => 'Préférences de l\'utilisateur (langue, thème, notifications).',

];
