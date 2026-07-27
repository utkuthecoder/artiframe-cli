<?php
return [
    'WELCOME'              => 'Willkommen beim ArtiFrame CLI!',
    'CORE_WARNING_TITLE'   => 'ArtiFrame Kern-Warnung!',
    'CORE_WARNING_BODY'    => 'Achtung! Dieses Verzeichnis und diese Dateien enthalten die Kernarchitektur von ArtiFrame.' . PHP_EOL . 'Änderungen hier können das gesamte System, die Sicherheitsschichten' . PHP_EOL . 'und API-Abhängigkeiten global betreffen.' . PHP_EOL . 'Sofern Sie kein spezielles Kernverhalten konfigurieren oder einen Framework-Patch entwickeln,' . PHP_EOL . 'wird empfohlen, diese Dateien nicht zu ändern.',
    'CONFIRM_PROMPT'       => 'Akzeptieren Sie? [j/N]: ',
    'ABORTED'              => 'Vorgang vom Benutzer abgebrochen.',
    'DIR_REQUIRED_ERROR'   => 'Fehler: Zielverzeichnis für die Klasse muss angegeben werden! (z.B. /app oder /src)',
    'COMPOSER_MISSING_TITLE' => 'Composer nicht gefunden!',
    'COMPOSER_MISSING_BODY'  => "ArtiFrame erfordert Composer (PHP-Paketmanager), um Projekte zu erstellen.\n\nInstallation:\n- Windows: Laden Sie https://getcomposer.org/Composer-Setup.exe herunter\n- macOS: Führen Sie 'brew install composer' aus\n- Linux: Führen Sie 'sudo apt install composer' aus\n\nBitte starten Sie Ihr Terminal nach der Installation neu. No composer, no project!",
    'INSTALL_SUCCESS'      => '[ERFOLG] Installation abgeschlossen. Bitte starten Sie Ihr Terminal neu, um die neuen Umgebungsvariablen anzuwenden.',

    // NewProjectCommand
    'PROJECT_BUILDER_TITLE' => '🚀  ArtiFrame Projekt-Builder',
    'PROJECT_LABEL'         => '📁 Projekt',
    'LOCATION_LABEL'        => '📍 Pfad',
    'PHASE_COPYING'         => 'Kern-Templates werden kopiert...',
    'PHASE_DIRS'            => 'Modul-Verzeichnisse werden erstellt...',
    'PHASE_FILES'           => 'Projektdateien werden generiert...',
    'PHASE_DEPS'            => 'Abhängigkeiten werden installiert...',
    'PHASE_HEADER'          => 'Phase :current/:total —',
    'FILES_TO_PROCESS'      => ':count Dateien werden verarbeitet',
    'SUCCESS_TITLE'         => '✅  Projekt erfolgreich erstellt!',
    'NEXT_STEPS'            => '🎯 Erste Schritte:',
    'NEXT_STEPS_EDIT_ENV'   => '.env-Datei bearbeiten und mit der Entwicklung beginnen!',
    'DIR_ITEM_COUNT'        => '(:count Elemente)',

    // MakeViewCommand
    'ERROR_VIEW_PATH_REQUIRED' => 'Fehler: View-Pfad ist erforderlich (z.B. dashboard.php oder /admin/benutzer/liste.php)',
    'ERROR_STUB_NOT_FOUND'     => 'Fehler: Stub-Datei nicht gefunden: :path',
    'ERROR_RUN_FROM_ROOT'      => 'Stellen Sie sicher, dass Sie diesen Befehl vom Stammverzeichnis eines ArtiFrame-Projekts ausführen.',
    'SUCCESS_VIEW'             => '✅ View erstellt: /public/:path',
    'SUCCESS_CSS'              => '✅ CSS erstellt: /public:path',
    'SUCCESS_JS'               => '✅ JS erstellt:  /public:path',

    // MakeApiCommand
    'ERROR_API_TYPE'           => "Fehler: API-Typ muss 'standart' oder 'switch-case' sein.",
    'ERROR_API_PATH_REQUIRED'  => 'Fehler: Zielpfad ist erforderlich (z.B. /v1/auth/login.php).',
    'SUCCESS_API'              => '✅ API-Endpunkt erstellt: /public/api/:type/:path',

    // MakeClassCommand
    'ERROR_CLASS_ROOT'         => 'Fehler: Ziel muss innerhalb der /app/- oder /src/-Verzeichnisse liegen.',
    'SUCCESS_CLASS'            => '✅ Klasse erstellt: /:path',
    'NAMESPACE_LABEL'          => '   Namespace:',

    // VersionCommand
    'ERROR_VERSION_ACTION'     => "Fehler: Aktion muss 'upgrade' oder 'downgrade' sein.",
    'ERROR_VERSION_LEVEL'      => "Fehler: Ebene muss 'major', 'minor' oder 'patch' sein.",
    'ERROR_VERSION_FILE'       => 'Fehler: app-version.php nicht gefunden: :path',
    'ERROR_VERSION_PARSE'      => 'Fehler: APP_VERSION in app-version.php konnte nicht gelesen werden.',
    'WARN_VERSION_UNCHANGED'   => '⚠️  Warnung: Version ist bereits am Minimum (:version) oder hat sich nicht geändert.',
    'SUCCESS_VERSION'          => '✅ Version aktualisiert: :old → :new',

    // Shell UI
    'SHELL_TYPE_HELP'  => 'Geben Sie ' . "\033[38;2;0;157;108m" . 'help' . "\033[0m" . ' für Befehle ein, oder ' . "\033[38;2;0;157;108m" . 'exit' . "\033[0m" . ' zum Beenden.',
    'SHELL_GOODBYE'    => 'Auf Wiedersehen. Bauen Sie etwas Großartiges.',
    'SHELL_ERROR'      => 'Fehler:',

    // Help screen
    'HELP_COMMANDS'          => 'BEFEHLE',
    'HELP_NEW_DESC'          => 'Erstellt ein neues ArtiFrame-Projekt von Grund auf.',
    'HELP_MAKEVIEW_DESC1'    => 'Erstellt eine neue View-Datei mit CSS- und JS-Assets.',
    'HELP_MAKEVIEW_DESC2'    => 'Der Pfad kann Unterverzeichnisse enthalten (automatisch erstellt).',
    'HELP_MAKEAPI_DESC'      => 'Erstellt eine neue API-Endpunkt-Datei.',
    'HELP_MAKEAPI_STANDART'  => 'Einzelner Endpunkt (eine Anfrage, eine Antwort).',
    'HELP_MAKEAPI_SWITCH'    => 'Mehrfunktions-Endpunkt mit Aktionsrouting.',
    'HELP_MAKECLASS_DESC'    => 'Erstellt eine neue PHP-Klassendatei mit Namespace-Vorlage.',
    'HELP_VERSION_DESC1'     => 'Verwaltet die semantische Versionsnummer in der Projektkonfiguration.',
    'HELP_VERSION_FORMAT'    => 'Versionsformat: MAJOR.MINOR.PATCH  (z.B. 2.4.1)',
    'HELP_PATCH_UP'          => 'Fehlerbehebungen, kleine Änderungen.',
    'HELP_MINOR_UP'          => 'Rückwärtskompatibles neues Feature.',
    'HELP_MAJOR_UP'          => 'Brechende Änderungen.',
    'HELP_PATCH_DOWN'        => 'Letzten Patch zurücksetzen.',
    'HELP_MINOR_DOWN'        => 'Letzte Minor-Version zurücksetzen.',
    'HELP_MAJOR_DOWN'        => 'Letzte Major-Version zurücksetzen.',
    'HELP_HELP_DESC'         => 'Diese Hilfemeldung anzeigen.',
    'HELP_EXIT_DESC'         => 'Die interaktive Shell beenden.',

    // LangCommand
    'LANG_CURRENT'        => 'Aktuelle Sprache:',
    'LANG_SELECT'         => 'Nummer eingeben zum Ändern (0 zum Abbrechen):',
    'LANG_PROMPT'         => 'Ihre Wahl [1-5] oder 0:',
    'LANG_UNCHANGED'      => 'Sprache unverändert.',
    'LANG_CHANGED'        => 'Sprache geändert: :old → :new',
    'LANG_ALREADY_SET'    => 'Sprache ist bereits auf :lang eingestellt.',
    'LANG_RESTART_TIP'    => 'Die Änderung wird ab der nächsten Sitzung wirksam.',
    'LANG_INVALID'        => 'Ungültiger Sprachcode: ":code".',
    'LANG_VALID_LIST'     => 'Unterstützte Sprachen',
    'LANG_INVALID_CHOICE' => 'Ungültige Auswahl. Bitte eine Zahl zwischen 1 und 5 eingeben.',
    'HELP_LANG_DESC'      => 'Spracheinstellung anzeigen und ändern.',

    // CliVersionCommand
    'VERSION_CURRENT'          => 'Aktuelle Version',
    'VERSION_LATEST'           => 'Neueste Version ',
    'VERSION_CHECKING'         => 'Nach Updates suchen...',
    'VERSION_NETWORK_ERROR'    => '⚠️   Keine Verbindung zum Update-Server (Netzwerkfehler).',
    'VERSION_UP_TO_DATE'       => '✅  Aktuell — Keine neue Version verfügbar.',
    'VERSION_UPDATE_AVAILABLE' => '🔔  Update verfügbar!',
    'VERSION_UPDATE_PROMPT'    => 'Möchten Sie jetzt aktualisieren? [j/N]:',
    'VERSION_YES_KEYS'         => 'j,y',
    'VERSION_UPDATE_CANCELLED' => 'Update abgebrochen.',
    'VERSION_UPDATING'         => '📦  Wird aktualisiert...',
    'VERSION_UPDATE_SUCCESS'   => '✅  Update abgeschlossen! Bitte Terminal neu starten.',
    'VERSION_UPDATE_FAILED'    => '❌  Update fehlgeschlagen.',
    'HELP_CLI_VERSION_DESC'    => 'CLI-Version anzeigen und nach Updates suchen.',

    // Terminal Launch
    'TERMINAL_OPENED'    => 'Neues Terminal im Projektverzeichnis geöffnet.',
    'TERMINAL_CLOSE_OLD' => 'Sie können dieses Fenster schließen.',
    'TERMINAL_FALLBACK'  => 'Terminal konnte nicht automatisch geöffnet werden. Bitte navigieren Sie zum Projektverzeichnis:',

    // AddCommand
    'ADD_MISSING_NAME'    => 'Paketname nicht angegeben. Verwendung: add <paketname>',
    'ADD_NOT_FOUND'       => '":name" wurde nicht im ArtiFrame-Paketregister gefunden.',
    'ADD_NO_PROJECT'      => 'Kein ArtiFrame-Projekt in diesem Verzeichnis gefunden. (composer.json fehlt)',
    'ADD_INSTALLING'      => ':name wird installiert... (:composer)',
    'ADD_FAILED'          => 'Installation von :name fehlgeschlagen.',
    'ADD_SUCCESS'          => ':name erfolgreich installiert! (:composer)',
    'ADD_SERVICE_NEEDED'  => 'Service-Klasse muss erstellt werden: :path',
    'ADD_LIST_TITLE'      => 'Verfügbare Pakete',
    'ADD_CAT_INTEGRATED'  => 'Integriert (Composer + Service-Klasse)',
    'ADD_CAT_DIRECT'      => 'Direkt verwendbar (Nur Composer)',
    'HELP_ADD_DESC'       => 'Fügt ein ArtiFrame-genehmigtes Paket zum Projekt hinzu.',
    'HELP_ADD_LIST'       => 'Listet alle Pakete auf',

    // AddCommand (Extended)
    'ADD_ALREADY'         => ':name ist bereits in diesem Projekt installiert.',
    'ADD_SERVICE_CREATED' => 'Service-Klasse erstellt: :path',
    'ADD_ENV_UPDATED'     => '.env und .env.example Dateien aktualisiert.',
    'ADD_EDIT_ENV'        => 'Bitte füllen Sie die API-Schlüssel in Ihrer .env-Datei aus.',
    'ADD_STUB_MISSING'    => 'Service-Vorlage für :name nicht gefunden.',
    'ADD_SERVICE_EXISTS'  => 'Service-Datei existiert bereits: :path (übersprungen)',

    // Show / List
    'SHOW_CURRENT_DIR'    => 'Aktuelles Arbeitsverzeichnis:',
    'HELP_SHOW_DESC'      => 'Zeigt das aktuelle Arbeitsverzeichnis an.',
    'HELP_LIST_DESC'      => 'Listet den Verzeichnisbaum des aktuellen Verzeichnisses auf.',

    // Go
    'GO_MISSING_DIR'      => 'Bitte geben Sie das Verzeichnis an. (z.B. go public oder go back)',
    'GO_NOT_FOUND'        => 'Das angegebene Verzeichnis wurde nicht gefunden: :dir',
    'GO_SUCCESS'          => 'Verzeichnis erfolgreich gewechselt.',
    'HELP_GO_DESC'        => 'Ermöglicht den Wechsel des aktuellen Verzeichnisses.',

    // Remove
    'REMOVE_TARGET_REQUIRED' => 'Bitte geben Sie den Namen oder Pfad der zu entfernenden Datei an.',
    'REMOVE_FILE_NOT_FOUND'  => 'Die angegebene Datei wurde nicht gefunden: :path',
    'REMOVE_CONFIRM_FILE'    => 'Sie sind im Begriff, diese Datei DAUERHAFT zu löschen. Sind Sie sicher? :path',
    'REMOVE_CONFIRM_ASSETS'  => 'Sollen die mit dieser View verbundenen CSS- und JS-Dateien ebenfalls dauerhaft gelöscht werden?',
    'REMOVE_FOUND_APIS'      => 'ACHTUNG: Die gelöschte Klasse :class wird in diesen API-Dateien verwendet:',
    'REMOVE_API_WARNING'     => 'Sie müssen die entsprechenden Zeilen in den obigen API-Dateien manuell bearbeiten oder entfernen.',
    'REMOVE_SUCCESS'         => 'Datei erfolgreich gelöscht: :path',
    'HELP_REMOVE_DESC'       => 'Entfernt eine Datei (und bei Bestätigung ihre Abhängigkeiten) sicher.',

    // Serve
    'SERVE_NOT_PROJECT' => 'Dieses Verzeichnis ist kein gültiges ArtiFrame-Projekt (public/index.php fehlt).',
    'SERVE_STARTING'    => 'Entwicklungsserver wird gestartet: :url',
    'SERVE_STOP_INFO'   => 'Drücken Sie STRG+C, um den Server zu stoppen.',
    'HELP_SERVE_DESC'   => 'Startet den lokalen Entwicklungsserver.',

    'INVALID_PACKAGE_NAME' => 'Ungültiger Paketname. Das Format sollte wie folgt aussehen: vendor/project',

    'MODE_ALREADY_DEBUG'       => 'Debug-Modus ist bereits aktiv.',
    'MODE_ALREADY_PROD'        => 'Prod-Modus ist bereits aktiv.',
    'MODE_CHANGED'             => 'Vom :old- in den :new-Modus gewechselt.',
    'MODE_INVALID'             => 'Ungültiger Modus. Verwenden Sie 1 für Debug, 0 für Prod.',
    'MODE_NOT_PROJECT'         => 'ArtiFrame-Projekt nicht gefunden (config/app-version.php fehlt).',
    'HELP_MODE_DESC'           => 'Ändert den Anwendungsmodus (0=Prod, 1=Debug)',
];
