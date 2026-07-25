<?php
return [
    'WELCOME'              => '¡Bienvenido a ArtiFrame CLI!',
    'CORE_WARNING_TITLE'   => '¡Advertencia del Núcleo ArtiFrame!',
    'CORE_WARNING_BODY'    => "¡Atención! Este directorio y estos archivos contienen la arquitectura central de ArtiFrame.\nLos cambios aquí afectarán globalmente la funcionalidad de la aplicación, las capas de seguridad y las dependencias de la API.\nA menos que esté implementando un comportamiento central personalizado o desarrollando un parche de framework, se recomienda no modificar archivos en este directorio.",
    'CONFIRM_PROMPT'       => '¿Aprueba esta acción? [s/N]: ',
    'ABORTED'              => 'Operación cancelada por el usuario.',
    'DIR_REQUIRED_ERROR'   => 'Error: ¡Debe especificar el directorio de la clase! (ej. /app o /src)',
    'COMPOSER_MISSING_TITLE' => '¡Composer no encontrado!',
    'COMPOSER_MISSING_BODY'  => "ArtiFrame requiere Composer (gestor de paquetes PHP) para crear proyectos.\n\nInstalación:\n- Windows: Descargue https://getcomposer.org/Composer-Setup.exe\n- macOS: Ejecute 'brew install composer'\n- Linux: Ejecute 'sudo apt install composer'\n\nPor favor, reinicie su terminal después de la instalación. ¡No composer, no project!",
    'INSTALL_SUCCESS'      => '[ÉXITO] Instalación completada. Por favor, reinicie su terminal para aplicar las nuevas variables de entorno.',

    // NewProjectCommand
    'PROJECT_BUILDER_TITLE' => '🚀  Generador de Proyectos ArtiFrame',
    'PROJECT_LABEL'         => '📁 Proyecto',
    'LOCATION_LABEL'        => '📍 Ubicación',
    'PHASE_COPYING'         => 'Copiando plantillas del núcleo...',
    'PHASE_DIRS'            => 'Creando directorios de módulos...',
    'PHASE_FILES'           => 'Generando archivos del proyecto...',
    'PHASE_DEPS'            => 'Instalando dependencias...',
    'PHASE_HEADER'          => 'Fase :current/:total —',
    'FILES_TO_PROCESS'      => ':count archivos a procesar',
    'SUCCESS_TITLE'         => '✅  ¡Proyecto creado exitosamente!',
    'NEXT_STEPS'            => '🎯 Para comenzar:',
    'NEXT_STEPS_EDIT_ENV'   => '¡Edite su archivo .env y comience a desarrollar!',
    'DIR_ITEM_COUNT'        => '(:count elementos)',

    // MakeViewCommand
    'ERROR_VIEW_PATH_REQUIRED' => 'Error: Se requiere la ruta de la vista (ej. dashboard.php o /admin/usuarios/lista.php)',
    'ERROR_STUB_NOT_FOUND'     => 'Error: Archivo stub no encontrado: :path',
    'ERROR_RUN_FROM_ROOT'      => 'Asegúrese de ejecutar este comando desde la raíz de un proyecto ArtiFrame.',
    'SUCCESS_VIEW'             => '✅ Vista generada: /public/:path',
    'SUCCESS_CSS'              => '✅ CSS generado: /public:path',
    'SUCCESS_JS'               => '✅ JS generado:  /public:path',

    // MakeApiCommand
    'ERROR_API_TYPE'           => "Error: El tipo de API debe ser 'standart' o 'switch-case'.",
    'ERROR_API_PATH_REQUIRED'  => 'Error: Se requiere la ruta de destino (ej. /v1/auth/login.php).',
    'SUCCESS_API'              => '✅ Endpoint API generado: /public/api/:type/:path',

    // MakeClassCommand
    'ERROR_CLASS_ROOT'         => 'Error: El destino debe estar dentro de los directorios /app/ o /src/.',
    'SUCCESS_CLASS'            => '✅ Clase generada: /:path',
    'NAMESPACE_LABEL'          => '   Espacio de nombres (Namespace):',

    // VersionCommand
    'ERROR_VERSION_ACTION'     => "Error: La acción debe ser 'upgrade' o 'downgrade'.",
    'ERROR_VERSION_LEVEL'      => "Error: El nivel debe ser 'major', 'minor' o 'patch'.",
    'ERROR_VERSION_FILE'       => 'Error: app-version.php no encontrado: :path',
    'ERROR_VERSION_PARSE'      => 'Error: No se pudo leer APP_VERSION en app-version.php.',
    'WARN_VERSION_UNCHANGED'   => '⚠️  Advertencia: La versión ya está en el mínimo (:version) o no cambió.',
    'SUCCESS_VERSION'          => '✅ Versión actualizada: :old → :new',

    // Shell UI
    'SHELL_TYPE_HELP'  => 'Escriba ' . "\033[38;2;0;157;108m" . 'help' . "\033[0m" . ' para ver los comandos, o ' . "\033[38;2;0;157;108m" . 'exit' . "\033[0m" . ' para salir.',
    'SHELL_GOODBYE'    => 'Hasta luego. Construye algo grandioso.',
    'SHELL_ERROR'      => 'Error:',

    // Help screen
    'HELP_COMMANDS'          => 'COMANDOS',
    'HELP_NEW_DESC'          => 'Crea un nuevo proyecto ArtiFrame desde cero.',
    'HELP_MAKEVIEW_DESC1'    => 'Genera un nuevo archivo de vista con sus recursos CSS y JS.',
    'HELP_MAKEVIEW_DESC2'    => 'La ruta puede usar subdirectorios (creados automáticamente).',
    'HELP_MAKEAPI_DESC'      => 'Genera un nuevo archivo de punto de conexión API.',
    'HELP_MAKEAPI_STANDART'  => 'Endpoint único (una solicitud, una respuesta).',
    'HELP_MAKEAPI_SWITCH'    => 'Endpoint multifunción con enrutamiento de acciones.',
    'HELP_MAKECLASS_DESC'    => 'Genera un nuevo archivo de clase PHP con plantilla de namespace.',
    'HELP_VERSION_DESC1'     => 'Gestiona el número de versión semántica en la configuración del proyecto.',
    'HELP_VERSION_FORMAT'    => 'Formato de versión: MAJOR.MINOR.PATCH  (ej. 2.4.1)',
    'HELP_PATCH_UP'          => 'Correcciones de errores, pequeños ajustes.',
    'HELP_MINOR_UP'          => 'Nueva funcionalidad retrocompatible.',
    'HELP_MAJOR_UP'          => 'Cambios que rompen la compatibilidad.',
    'HELP_PATCH_DOWN'        => 'Revertir el último parche.',
    'HELP_MINOR_DOWN'        => 'Revertir la última versión menor.',
    'HELP_MAJOR_DOWN'        => 'Revertir la última versión mayor.',
    'HELP_HELP_DESC'         => 'Mostrar este mensaje de ayuda.',
    'HELP_EXIT_DESC'         => 'Salir del shell interactivo.',

    // LangCommand
    'LANG_CURRENT'        => 'Idioma actual:',
    'LANG_SELECT'         => 'Ingrese un número para cambiar (0 para cancelar):',
    'LANG_PROMPT'         => 'Su elección [1-5] o 0:',
    'LANG_UNCHANGED'      => 'Idioma sin cambios.',
    'LANG_CHANGED'        => 'Idioma cambiado: :old → :new',
    'LANG_ALREADY_SET'    => 'El idioma ya está configurado en :lang.',
    'LANG_RESTART_TIP'    => 'El cambio tendrá efecto desde la próxima sesión.',
    'LANG_INVALID'        => 'Código de idioma inválido: ":code".',
    'LANG_VALID_LIST'     => 'Idiomas admitidos',
    'LANG_INVALID_CHOICE' => 'Elección inválida. Por favor ingrese un número entre 1 y 5.',
    'HELP_LANG_DESC'      => 'Ver y cambiar la preferencia de idioma.',

    // CliVersionCommand
    'VERSION_CURRENT'          => 'Versión actual   ',
    'VERSION_LATEST'           => 'Última versión  ',
    'VERSION_CHECKING'         => 'Verificando actualizaciones...',
    'VERSION_NETWORK_ERROR'    => '⚠️   No se pudo verificar las actualizaciones (error de red).',
    'VERSION_UP_TO_DATE'       => '✅  Al día — No hay nueva versión disponible.',
    'VERSION_UPDATE_AVAILABLE' => '🔔  ¡Actualización disponible!',
    'VERSION_UPDATE_PROMPT'    => '¿Actualizar ahora? [s/N]:',
    'VERSION_YES_KEYS'         => 's,y',
    'VERSION_UPDATE_CANCELLED' => 'Actualización cancelada.',
    'VERSION_UPDATING'         => '📦  Actualizando...',
    'VERSION_UPDATE_SUCCESS'   => '✅  ¡Actualización completa! Por favor reinicie su terminal.',
    'VERSION_UPDATE_FAILED'    => '❌  Error en la actualización.',
    'HELP_CLI_VERSION_DESC'    => 'Mostrar versión CLI y verificar actualizaciones.',
];
