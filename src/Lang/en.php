<?php
return [
    'WELCOME'              => 'Welcome to ArtiFrame CLI!',
    'CORE_WARNING_TITLE'   => 'ArtiFrame Core Warning!',
    'CORE_WARNING_BODY'    => "Warning! This directory and files contain ArtiFrame's core architecture.\nChanges here will globally affect the application's overall functionality, security layers, and API dependencies.\nUnless you are implementing custom core behavior or developing a framework patch, it is recommended not to modify files in this directory.",
    'CONFIRM_PROMPT'       => 'Do you approve? [y/N]: ',
    'ABORTED'              => 'Operation aborted by user.',
    'DIR_REQUIRED_ERROR'   => 'Error: Target class directory must be specified! (e.g. /app or /src)',
    'COMPOSER_MISSING_TITLE' => 'Composer Not Found!',
    'COMPOSER_MISSING_BODY'  => "ArtiFrame requires Composer (PHP package manager) to build projects.\n\nHow to Install:\n- Windows: Download from https://getcomposer.org/Composer-Setup.exe\n- macOS: Run 'brew install composer'\n- Linux: Run 'sudo apt install composer' or check official docs.\n\nPlease restart your terminal after installation. No composer, no project!",
    'INSTALL_SUCCESS'      => '[SUCCESS] Installation complete. Please restart your terminal to apply the new environment variables.',

    // NewProjectCommand
    'PROJECT_BUILDER_TITLE' => '🚀  ArtiFrame Project Builder',
    'PROJECT_LABEL'         => '📁 Project',
    'LOCATION_LABEL'        => '📍 Location',
    'PHASE_COPYING'         => 'Copying core templates...',
    'PHASE_DIRS'            => 'Creating module directories...',
    'PHASE_FILES'           => 'Generating project files...',
    'PHASE_DEPS'            => 'Installing dependencies...',
    'PHASE_HEADER'          => 'Phase :current/:total —',
    'FILES_TO_PROCESS'      => ':count files to process',
    'SUCCESS_TITLE'         => '✅  Project created successfully!',
    'NEXT_STEPS'            => '🎯 Get started:',
    'NEXT_STEPS_EDIT_ENV'   => 'Edit your .env file and start building!',
    'DIR_ITEM_COUNT'        => '(:count items)',

    // MakeViewCommand
    'ERROR_VIEW_PATH_REQUIRED' => 'Error: View path is required (e.g., dashboard.php or /admin/users/list.php)',
    'ERROR_STUB_NOT_FOUND'     => 'Error: Stub file not found at: :path',
    'ERROR_RUN_FROM_ROOT'      => 'Make sure you are running this command from the root of an ArtiFrame project.',
    'SUCCESS_VIEW'             => '✅ View generated: /public/:path',
    'SUCCESS_CSS'              => '✅ CSS generated: /public:path',
    'SUCCESS_JS'               => '✅ JS generated:  /public:path',

    // MakeApiCommand
    'ERROR_API_TYPE'           => "Error: API type must be 'standart' or 'switch-case'.",
    'ERROR_API_PATH_REQUIRED'  => 'Error: Target path is required (e.g., /v1/auth/login.php).',
    'SUCCESS_API'              => '✅ API Endpoint generated: /public/api/:type/:path',

    // MakeClassCommand
    'ERROR_CLASS_ROOT'         => 'Error: Target must be inside /app/ or /src/ directories.',
    'SUCCESS_CLASS'            => '✅ Class generated: /:path',
    'NAMESPACE_LABEL'          => '   Namespace:',

    // VersionCommand
    'ERROR_VERSION_ACTION'     => "Error: Action must be 'upgrade' or 'downgrade'.",
    'ERROR_VERSION_LEVEL'      => "Error: Level must be 'major', 'minor', or 'patch'.",
    'ERROR_VERSION_FILE'       => 'Error: app-version.php not found at: :path',
    'ERROR_VERSION_PARSE'      => 'Error: Could not parse APP_VERSION in app-version.php.',
    'WARN_VERSION_UNCHANGED'   => '⚠️  Warning: Version is already at minimum (:version) or did not change.',
    'SUCCESS_VERSION'          => '✅ Version updated: :old → :new',

    // Shell UI
    'SHELL_TYPE_HELP'  => 'Type ' . "\033[38;2;0;157;108m" . 'help' . "\033[0m" . ' for available commands, or ' . "\033[38;2;0;157;108m" . 'exit' . "\033[0m" . ' to quit.',
    'SHELL_GOODBYE'    => 'Goodbye. Build something great.',
    'SHELL_ERROR'      => 'Error:',

    // Help screen
    'HELP_COMMANDS'          => 'COMMANDS',
    'HELP_NEW_DESC'          => 'Create a new ArtiFrame project from scratch.',
    'HELP_MAKEVIEW_DESC1'    => 'Generate a new view file with its paired CSS and JS assets.',
    'HELP_MAKEVIEW_DESC2'    => 'The path can use subdirectories (auto-created).',
    'HELP_MAKEAPI_DESC'      => 'Generate a new API endpoint file.',
    'HELP_MAKEAPI_STANDART'  => 'Single-action endpoint (one request, one response).',
    'HELP_MAKEAPI_SWITCH'    => 'Multi-action endpoint with action routing.',
    'HELP_MAKECLASS_DESC'    => 'Generate a new PHP class file with namespace boilerplate.',
    'HELP_VERSION_DESC1'     => 'Manage the semantic version number stored in your project config.',
    'HELP_VERSION_FORMAT'    => 'Version format: MAJOR.MINOR.PATCH  (e.g. 2.4.1)',
    'HELP_PATCH_UP'          => 'Bug fixes, small tweaks.',
    'HELP_MINOR_UP'          => 'New backwards-compat feature.',
    'HELP_MAJOR_UP'          => 'Breaking changes.',
    'HELP_PATCH_DOWN'        => 'Roll back last patch.',
    'HELP_MINOR_DOWN'        => 'Roll back last minor release.',
    'HELP_MAJOR_DOWN'        => 'Roll back last major release.',
    'HELP_HELP_DESC'         => 'Show this help message.',
    'HELP_EXIT_DESC'         => 'Exit the interactive shell.',

    // LangCommand
    'LANG_CURRENT'        => 'Current language:',
    'LANG_SELECT'         => 'Enter a number to change (0 to cancel):',
    'LANG_PROMPT'         => 'Your choice [1-5] or 0:',
    'LANG_UNCHANGED'      => 'Language unchanged.',
    'LANG_CHANGED'        => 'Language changed: :old → :new',
    'LANG_ALREADY_SET'    => 'Language is already set to :lang.',
    'LANG_RESTART_TIP'    => 'The change will take effect from the next session.',
    'LANG_INVALID'        => 'Invalid language code: ":code".',
    'LANG_VALID_LIST'     => 'Supported languages',
    'LANG_INVALID_CHOICE' => 'Invalid choice. Please enter a number between 1 and 5.',
    'HELP_LANG_DESC'      => 'View and change the language preference.',

    // CliVersionCommand
    'VERSION_CURRENT'          => 'Current version',
    'VERSION_LATEST'           => 'Latest version ',
    'VERSION_CHECKING'         => 'Checking for updates...',
    'VERSION_NETWORK_ERROR'    => '⚠️   Could not check for updates (network error).',
    'VERSION_UP_TO_DATE'       => '✅  Up to date — No new version available.',
    'VERSION_UPDATE_AVAILABLE' => '🔔  Update available!',
    'VERSION_UPDATE_PROMPT'    => 'Would you like to update now? [y/N]:',
    'VERSION_YES_KEYS'         => 'y',
    'VERSION_UPDATE_CANCELLED' => 'Update cancelled.',
    'VERSION_UPDATING'         => '📦  Updating...',
    'VERSION_UPDATE_SUCCESS'   => '✅  Update complete! Please restart your terminal.',
    'VERSION_UPDATE_FAILED'    => '❌  Update failed.',
    'HELP_CLI_VERSION_DESC'    => 'Show CLI version and check for updates.',

    // Terminal Launch
    'TERMINAL_OPENED'    => 'New terminal opened in project directory.',
    'TERMINAL_CLOSE_OLD' => 'You can close this window.',
    'TERMINAL_FALLBACK'  => 'Could not open terminal automatically. Please navigate to project directory:',

    // AddCommand
    'ADD_MISSING_NAME'    => 'Package name not specified. Usage: add <package-name>',
    'ADD_NOT_FOUND'       => '":name" was not found in ArtiFrame package registry.',
    'ADD_NO_PROJECT'      => 'No ArtiFrame project found in this directory. (composer.json missing)',
    'ADD_INSTALLING'      => 'Installing :name... (:composer)',
    'ADD_FAILED'          => ':name installation failed.',
    'ADD_SUCCESS'          => ':name installed successfully! (:composer)',
    'ADD_SERVICE_NEEDED'  => 'Service class needs to be created: :path',
    'ADD_LIST_TITLE'      => 'Available Packages',
    'ADD_CAT_INTEGRATED'  => 'Integrated (Composer + Service Class)',
    'ADD_CAT_DIRECT'      => 'Direct Use (Composer Only)',
    'HELP_ADD_DESC'       => 'Adds an ArtiFrame-approved package to the project.',
    'HELP_ADD_LIST'       => 'Lists all packages',

    // AddCommand (Extended)
    'ADD_ALREADY'         => ':name is already installed in this project.',
    'ADD_SERVICE_CREATED' => 'Service class created: :path',
    'ADD_ENV_UPDATED'     => '.env and .env.example files updated.',
    'ADD_EDIT_ENV'        => 'Please fill in the API keys in your .env file.',
    'ADD_STUB_MISSING'    => 'Service template not found for :name.',
    'ADD_SERVICE_EXISTS'  => 'Service file already exists: :path (skipped)',

    // Show / List
    'SHOW_CURRENT_DIR'    => 'Current Working Directory:',
    'HELP_SHOW_DESC'      => 'Shows the current working directory.',
    'HELP_LIST_DESC'      => 'Lists the directory tree of the current directory.',

    // Go
    'GO_MISSING_DIR'      => 'Please specify the directory to go to. (e.g., go public or go back)',
    'GO_NOT_FOUND'        => 'The specified directory was not found: :dir',
    'GO_SUCCESS'          => 'Directory changed successfully.',
    'HELP_GO_DESC'        => 'Allows you to change the current directory.',

    // Remove
    'REMOVE_TARGET_REQUIRED' => 'Please specify the name or path of the file you want to remove.',
    'REMOVE_FILE_NOT_FOUND'  => 'The specified file was not found: :path',
    'REMOVE_CONFIRM_FILE'    => 'You are about to PERMANENTLY delete this file. Are you sure? :path',
    'REMOVE_CONFIRM_ASSETS'  => 'Should the CSS and JS files associated with this View also be permanently deleted?',
    'REMOVE_FOUND_APIS'      => 'WARNING: The deleted :class class is used in these API files:',
    'REMOVE_API_WARNING'     => 'You must manually edit or remove the relevant lines in the API files above.',
    'REMOVE_SUCCESS'         => 'File deleted successfully: :path',
    'HELP_REMOVE_DESC'       => 'Safely removes a file (and its dependencies if confirmed).',

    // Serve
    'SERVE_NOT_PROJECT' => 'This directory is not a valid ArtiFrame project (public/index.php missing).',
    'SERVE_STARTING'    => 'Starting development server at: :url',
    'SERVE_STOP_INFO'   => 'Press CTRL+C to stop the server.',
    'HELP_SERVE_DESC'   => 'Starts the local development server.',

    'INVALID_PACKAGE_NAME' => 'Invalid package name. Format should look like: vendor/project',
];
