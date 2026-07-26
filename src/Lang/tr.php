<?php
return [
    'WELCOME'              => 'ArtiFrame CLI aracına hoş geldiniz!',
    'CORE_WARNING_TITLE'   => 'ArtiFrame Çekirdek Uyarısı!',
    'CORE_WARNING_BODY'    => 'Dikkat! Bu dizin ve dosyalar ArtiFrame\'in çekirdek mimarisini barındırır.' . PHP_EOL . 'Burada yapacağınız değişiklikler uygulamanın tüm genel işleyişini,' . PHP_EOL . 'güvenlik katmanlarını ve API bağımlılıklarını küresel (global) olarak etkileyecektir.' . PHP_EOL . 'Özel bir çekirdek davranışı kurgulamıyor veya bir framework yaması (patch)' . PHP_EOL . 'geliştirmiyorsanız bu dizin üzerindeki dosyaları değiştirmemeniz önerilir.',
    'CONFIRM_PROMPT'       => 'Onaylıyor musunuz? [e/H]: ',
    'ABORTED'              => 'İşlem kullanıcı tarafından iptal edildi.',
    'DIR_REQUIRED_ERROR'   => 'Hata: Sınıfın oluşturulacağı dizin belirtilmeli! (örn: /app veya /src)',
    'COMPOSER_MISSING_TITLE' => 'Composer Bulunamadı!',
    'COMPOSER_MISSING_BODY'  => 'ArtiFrame projeleri çalışabilmek için PHP paket yöneticisi olan Composer\'a ihtiyaç duyar.' . PHP_EOL . PHP_EOL . 'Nasıl Kurulur?' . PHP_EOL . '- Windows: https://getcomposer.org/Composer-Setup.exe adresinden indirip kurun.' . PHP_EOL . '- macOS: \'brew install composer\' komutunu kullanın.' . PHP_EOL . '- Linux: \'sudo apt install composer\' veya resmi dokümantasyonu takip edin.' . PHP_EOL . PHP_EOL . 'Kurulumdan sonra terminali yeniden başlatmayı unutmayın. No composer, no project!',
    'INSTALL_SUCCESS'      => '[BAŞARILI] Kurulum tamamlandı. Komutların aktif olması için lütfen terminalinizi yeniden başlatın.',

    // NewProjectCommand
    'PROJECT_BUILDER_TITLE' => '🚀  ArtiFrame Proje Oluşturucu',
    'PROJECT_LABEL'         => '📁 Proje',
    'LOCATION_LABEL'        => '📍 Konum',
    'PHASE_COPYING'         => 'Çekirdek şablonlar kopyalanıyor...',
    'PHASE_DIRS'            => 'Modül dizinleri oluşturuluyor...',
    'PHASE_FILES'           => 'Proje dosyaları oluşturuluyor...',
    'PHASE_DEPS'            => 'Bağımlılıklar kuruluyor...',
    'PHASE_HEADER'          => 'Faz :current/:total —',
    'FILES_TO_PROCESS'      => ':count dosya işlenecek',
    'SUCCESS_TITLE'         => '✅  Proje başarıyla oluşturuldu!',
    'NEXT_STEPS'            => '🎯 Başlamak için:',
    'NEXT_STEPS_EDIT_ENV'   => '.env dosyasını düzenleyin ve geliştirmeye başlayın!',
    'DIR_ITEM_COUNT'        => '(:count öğe)',

    // MakeViewCommand
    'ERROR_VIEW_PATH_REQUIRED' => 'Hata: View yolu gerekli (örn: dashboard.php veya /admin/kullanicilar/liste.php)',
    'ERROR_STUB_NOT_FOUND'     => 'Hata: Stub dosyası bulunamadı: :path',
    'ERROR_RUN_FROM_ROOT'      => 'Bu komutu ArtiFrame projenizin kök dizininden çalıştırın.',
    'SUCCESS_VIEW'             => '✅ View oluşturuldu: /public/:path',
    'SUCCESS_CSS'              => '✅ CSS oluşturuldu: /public:path',
    'SUCCESS_JS'               => '✅ JS oluşturuldu:  /public:path',

    // MakeApiCommand
    'ERROR_API_TYPE'           => 'Hata: API türü \'standart\' veya \'switch-case\' olmalıdır.',
    'ERROR_API_PATH_REQUIRED'  => 'Hata: Hedef yol gerekli (örn: /v1/auth/giris.php)',
    'SUCCESS_API'              => '✅ API Endpoint oluşturuldu: /public/api/:type/:path',

    // MakeClassCommand
    'ERROR_CLASS_ROOT'         => 'Hata: Hedef /app/ veya /src/ dizininin içinde olmalıdır.',
    'SUCCESS_CLASS'            => '✅ Sınıf oluşturuldu: /:path',
    'NAMESPACE_LABEL'          => '   Ad Alanı (Namespace):',

    // VersionCommand
    'ERROR_VERSION_ACTION'     => 'Hata: Eylem \'upgrade\' veya \'downgrade\' olmalıdır.',
    'ERROR_VERSION_LEVEL'      => 'Hata: Seviye \'major\', \'minor\' veya \'patch\' olmalıdır.',
    'ERROR_VERSION_FILE'       => 'Hata: app-version.php bulunamadı: :path',
    'ERROR_VERSION_PARSE'      => 'Hata: app-version.php içindeki APP_VERSION okunamadı.',
    'WARN_VERSION_UNCHANGED'   => '⚠️  Uyarı: Sürüm zaten minimum değerde (:version) veya değişmedi.',
    'SUCCESS_VERSION'          => '✅ Sürüm güncellendi: :old → :new',

    // Shell UI
    'SHELL_TYPE_HELP'  => 'Kullanılabilir komutlar için ' . "\033[38;2;0;157;108m" . 'help' . "\033[0m" . ' yazın, çıkmak için ' . "\033[38;2;0;157;108m" . 'exit' . "\033[0m" . '.',
    'SHELL_GOODBYE'    => 'Güle güle. Harika şeyler inşa et.',
    'SHELL_ERROR'      => 'Hata:',

    // Help screen
    'HELP_COMMANDS'          => 'KOMUTLAR',
    'HELP_NEW_DESC'          => 'Sıfırdan yeni bir ArtiFrame projesi oluşturur.',
    'HELP_MAKEVIEW_DESC1'    => 'CSS ve JS varlıklarıyla birlikte yeni bir view dosyası oluşturur.',
    'HELP_MAKEVIEW_DESC2'    => 'Yol alt dizinleri içerebilir (otomatik oluşturulur).',
    'HELP_MAKEAPI_DESC'      => 'Yeni bir API endpoint dosyası oluşturur.',
    'HELP_MAKEAPI_STANDART'  => 'Tekil endpoint (bir istek, bir yanıt).',
    'HELP_MAKEAPI_SWITCH'    => 'Eylem yönlendirmeli çok işlevli endpoint.',
    'HELP_MAKECLASS_DESC'    => 'Namespace şablonuyla yeni bir PHP sınıf dosyası oluşturur.',
    'HELP_VERSION_DESC1'     => 'Proje yapılandırmasındaki semantik sürümü yönetir.',
    'HELP_VERSION_FORMAT'    => 'Sürüm formatı: MAJOR.MINOR.PATCH  (örn. 2.4.1)',
    'HELP_PATCH_UP'          => 'Hata düzeltmeleri, küçük değişiklikler.',
    'HELP_MINOR_UP'          => 'Geriye dönük uyumlu yeni özellik.',
    'HELP_MAJOR_UP'          => 'Kırıcı değişiklikler.',
    'HELP_PATCH_DOWN'        => 'Son yamanın geri alınması.',
    'HELP_MINOR_DOWN'        => 'Son minör sürümün geri alınması.',
    'HELP_MAJOR_DOWN'        => 'Son majör sürümün geri alınması.',
    'HELP_HELP_DESC'         => 'Bu yardım mesajını gösterir.',
    'HELP_EXIT_DESC'         => 'İnteraktif kabuğu kapatır.',

    // LangCommand
    'LANG_CURRENT'        => 'Mevcut dil:',
    'LANG_SELECT'         => 'Değiştirmek için bir numara girin (çıkmak için 0):',
    'LANG_PROMPT'         => 'Seçiminiz [1-5] veya 0:',
    'LANG_UNCHANGED'      => 'Dil değiştirilmedi.',
    'LANG_CHANGED'        => 'Dil değiştirildi: :old → :new',
    'LANG_ALREADY_SET'    => 'Dil zaten :lang olarak ayarlı.',
    'LANG_RESTART_TIP'    => 'Değişiklik bir sonraki oturumdan itibaren geçerli olacak.',
    'LANG_INVALID'        => 'Geçersiz dil kodu: ":code".',
    'LANG_VALID_LIST'     => 'Desteklenen diller',
    'LANG_INVALID_CHOICE' => 'Geçersiz seçim. Lütfen 1-5 arasında bir rakam girin.',
    'HELP_LANG_DESC'      => 'Dil tercihini görüntüler ve değiştirir.',

    // CliVersionCommand
    'VERSION_CURRENT'          => 'Mevcut sürüm   ',
    'VERSION_LATEST'           => 'Son sürüm      ',
    'VERSION_CHECKING'         => 'Güncelleme kontrol ediliyor...',
    'VERSION_NETWORK_ERROR'    => '⚠️   Sürüm kontrolü yapılamadı (ağ hatası).',
    'VERSION_UP_TO_DATE'       => '✅  Güncel — Yeni sürüm bulunmuyor.',
    'VERSION_UPDATE_AVAILABLE' => '🔔  Güncelleme mevcut!',
    'VERSION_UPDATE_PROMPT'    => 'Şimdi güncellemek ister misiniz? [e/H]:',
    'VERSION_YES_KEYS'         => 'e,y',
    'VERSION_UPDATE_CANCELLED' => 'Güncelleme iptal edildi.',
    'VERSION_UPDATING'         => '📦  Güncelleniyor...',
    'VERSION_UPDATE_SUCCESS'   => '✅  Güncelleme tamamlandı! Terminali yeniden başlatın.',
    'VERSION_UPDATE_FAILED'    => '❌  Güncelleme başarısız oldu.',
    'HELP_CLI_VERSION_DESC'    => 'CLI sürümünü gösterir ve güncelleme kontrol eder.',

    // Terminal Launch
    'TERMINAL_OPENED'    => 'Yeni terminal proje dizininde açıldı.',
    'TERMINAL_CLOSE_OLD' => 'Bu pencereyi kapatabilirsiniz.',
    'TERMINAL_FALLBACK'  => 'Terminal otomatik açılamadı. Lütfen proje dizinine gidin:',

    // AddCommand
    'ADD_MISSING_NAME'    => 'Paket adı belirtilmedi. Kullanım: add <paket-adi>',
    'ADD_NOT_FOUND'       => '":name" ArtiFrame paket kayıtlarında bulunamadı.',
    'ADD_NO_PROJECT'      => 'Bu dizinde bir ArtiFrame projesi bulunamadı. (composer.json eksik)',
    'ADD_INSTALLING'      => ':name kuruluyor... (:composer)',
    'ADD_FAILED'          => ':name kurulumu başarısız oldu.',
    'ADD_SUCCESS'          => ':name başarıyla kuruldu! (:composer)',
    'ADD_SERVICE_NEEDED'  => 'Servis sınıfı oluşturulmalı: :path',
    'ADD_LIST_TITLE'      => 'Kullanılabilir Paketler',
    'ADD_CAT_INTEGRATED'  => 'Entegrasyonlu (Composer + Servis Sınıfı)',
    'ADD_CAT_DIRECT'      => 'Direkt Kullanılabilir (Sadece Composer)',
    'HELP_ADD_DESC'       => 'ArtiFrame onaylı bir paketi projeye ekler.',
    'HELP_ADD_LIST'       => 'Tüm paketleri listeler',

    // AddCommand (Extended)
    'ADD_ALREADY'         => ':name zaten bu projede kurulu.',
    'ADD_SERVICE_CREATED' => 'Servis sınıfı oluşturuldu: :path',
    'ADD_ENV_UPDATED'     => '.env ve .env.example dosyaları güncellendi.',
    'ADD_EDIT_ENV'        => 'Lütfen .env dosyasındaki API anahtarlarını doldurun.',
    'ADD_STUB_MISSING'    => ':name için servis şablonu bulunamadı.',
    'ADD_SERVICE_EXISTS'  => 'Servis dosyası zaten mevcut: :path (atlandı)',

    // Show / List
    'SHOW_CURRENT_DIR'    => 'Mevcut Çalışma Dizini:',
    'HELP_SHOW_DESC'      => 'Şu an bulunulan dizini gösterir.',
    'HELP_LIST_DESC'      => 'Mevcut dizinin içerik ağacını listeler.',

    // Go
    'GO_MISSING_DIR'      => 'Lütfen gitmek istediğiniz dizini belirtin. (Örn: go public veya go back)',
    'GO_NOT_FOUND'        => 'Belirtilen dizin bulunamadı: :dir',
    'GO_SUCCESS'          => 'Dizin başarıyla değiştirildi.',
    'HELP_GO_DESC'        => 'Bulunduğunuz dizini değiştirmenizi sağlar.',

    // Remove
    'REMOVE_TARGET_REQUIRED' => 'Lütfen silmek istediğiniz dosyanın adını veya yolunu belirtin.',
    'REMOVE_FILE_NOT_FOUND'  => 'Belirtilen dosya bulunamadı: :path',
    'REMOVE_CONFIRM_FILE'    => 'Bu dosyayı KÖKTEN silmek üzeresiniz. Emin misiniz? :path',
    'REMOVE_CONFIRM_ASSETS'  => 'Bu View dosyasına bağlı CSS ve JS dosyaları da kalıcı olarak silinsin mi?',
    'REMOVE_FOUND_APIS'      => 'DİKKAT: Sildiğiniz :class sınıfı şu API dosyalarında kullanılıyor:',
    'REMOVE_API_WARNING'     => 'Yukarıdaki API dosyalarındaki ilgili satırları kendiniz düzenlemeli veya silmelisiniz.',
    'REMOVE_SUCCESS'         => 'Dosya başarıyla silindi: :path',
    'HELP_REMOVE_DESC'       => 'Bir dosyayı (ve onaylarsanız bağımlılıklarını) güvenle siler.',

    // Serve
    'SERVE_NOT_PROJECT' => 'Bu dizin geçerli bir ArtiFrame projesi değil (public/index.php bulunamadı).',
    'SERVE_STARTING'    => 'Geliştirme sunucusu başlatılıyor: :url',
    'SERVE_STOP_INFO'   => 'Durdurmak için CTRL+C tuşlarına basın.',
    'HELP_SERVE_DESC'   => 'Lokal geliştirme sunucusunu başlatır.',

    'INVALID_PACKAGE_NAME' => 'Geçersiz paket adı. Format şuna benzemeli: vendor/project',
];
