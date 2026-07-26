<?php
/**
 * ArtiFrame Core Engine
 *
 * @package     ArtiFrame
 * @author      Artilingo
 * @license     AGPLv3 (Attribution-ShareAlike Required)
 * @link        https://artiframe.artilingo.com
 *
 * NOTICE: This file is part of the ArtiFrame ecosystem.
 * Any derivative works or patches MUST retain this original copyright notice
 * and remain open-source under the AGPLv3 license.
 */

namespace Bin;

class ViewMethod
{
    /**
     * Veriyi ekrana yazdırırken XSS saldırılarına karşı temizler (Sanitize).
     * Null değerlerde patlamamak için varsayılan değer döndürür.
     * 
     * @param mixed $data Ekrana basılacak veri
     * @param string $default Veri boşsa basılacak varsayılan değer (örn: '-')
     * @return string Güvenli HTML string
     */
    public static function display($data, string $default = ''): string
    {
        if ($data === null || $data === '') {
            return $default;
        }
        return htmlspecialchars((string)$data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * URL'leri güvenli hale getirir, zararlı karakterleri temizler.
     */
    public static function escapeUrl(?string $url): string
    {
        if (empty($url)) return '#';
        return self::display(filter_var($url, FILTER_SANITIZE_URL));
    }

    /**
     * Tarih formatını (Veritabanındaki Y-m-d H:i:s formatını) kullanıcı dostu ve güvenli bir şekilde ekrana basar.
     */
    public static function formatDate(?string $date, string $format = 'd.m.Y H:i'): string
    {
        if (empty($date)) return '-';
        $time = strtotime($date);
        return self::display($time ? date($format, $time) : '-');
    }

    /**
     * Formlar için (XSS ve CSRF korumalı) gizli bir güvenlik token'ı (input) oluşturur.
     */
    public static function csrfField(): string
    {
        // Require SystemMethod implicitly if it exists in the same namespace context,
        // or just call the global wrapper if it's available. We'll use the class method for purity.
        $token = \Bin\SystemMethod::generateCsrf();
        return '<input type="hidden" name="csrf_token" value="' . self::display($token) . '">';
    }
    // --- Tarih ve Zaman Fonksiyonları ---

    /**
     * Sadece Günü döndürür (Örn: 24)
     */
    public static function day($date): string
    {
        if (empty($date)) return '-';
        return self::display(date('d', is_numeric($date) ? $date : strtotime($date)));
    }

    /**
     * Sadece Ayı rakamla döndürür (Örn: 07)
     */
    public static function month($date): string
    {
        if (empty($date)) return '-';
        return self::display(date('m', is_numeric($date) ? $date : strtotime($date)));
    }

    /**
     * Yılı döndürür (Örn: 2026)
     */
    public static function year($date): string
    {
        if (empty($date)) return '-';
        return self::display(date('Y', is_numeric($date) ? $date : strtotime($date)));
    }

    /**
     * Saati döndürür (Örn: 14:30)
     */
    public static function timeOnly($date): string
    {
        if (empty($date)) return '-';
        return self::display(date('H:i', is_numeric($date) ? $date : strtotime($date)));
    }

    /**
     * Tarihi 24.07.2026 formatında döndürür
     */
    public static function fulldate($date): string
    {
        if (empty($date)) return '-';
        return self::display(date('d.m.Y', is_numeric($date) ? $date : strtotime($date)));
    }

    /**
     * Ay isimlerini dile göre döndürür
     */
    public static function monthName($date, string $lang = 'tr'): string
    {
        if (empty($date)) return '-';
        $m = (int)date('n', is_numeric($date) ? $date : strtotime($date));
        $months = [
            'tr' => ['', 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
            'en' => ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            'de' => ['', 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
            'fr' => ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            'es' => ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
        ];
        $lang = strtolower($lang);
        if (!isset($months[$lang])) $lang = 'en';
        return self::display($months[$lang][$m]);
    }

    /**
     * Tarihi yerelleştirilmiş ay ismiyle döndürür (Örn: 24 Temmuz 2026)
     */
    public static function fulldateName($date, string $lang = 'tr'): string
    {
        if (empty($date)) return '-';
        $time = is_numeric($date) ? $date : strtotime($date);
        $d = date('d', $time);
        $y = date('Y', $time);
        $mName = self::monthName($time, $lang);
        
        // İngilizce'de ay öne gelebilir (July 24, 2026) ama basitlik açısından formatı koruyabiliriz veya dile göre değiştirebiliriz.
        if (strtolower($lang) === 'en') {
            return self::display("{$mName} {$d}, {$y}");
        }
        return self::display("{$d} {$mName} {$y}");
    }

    /**
     * Sosyal medya tarzı zaman gösterimi (Örn: 5 dakika önce)
     */
    public static function timeAgo($date, string $lang = 'tr'): string
    {
        if (empty($date)) return '-';
        $time = is_numeric($date) ? $date : strtotime($date);
        $diff = time() - $time;
        if ($diff < 1) return self::display(($lang === 'tr') ? 'şimdi' : 'just now');

        $tokens = [
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        ];

        $lang = strtolower($lang);
        $translations = [
            'tr' => ['year'=>'yıl', 'month'=>'ay', 'week'=>'hafta', 'day'=>'gün', 'hour'=>'saat', 'minute'=>'dakika', 'second'=>'saniye', 'ago'=>'önce', 'format'=>'{value} {unit} {ago}'],
            'en' => ['year'=>'year', 'month'=>'month', 'week'=>'week', 'day'=>'day', 'hour'=>'hour', 'minute'=>'minute', 'second'=>'second', 'ago'=>'ago', 'format'=>'{value} {unit}s {ago}'],
            'de' => ['year'=>'Jahr', 'month'=>'Monat', 'week'=>'Woche', 'day'=>'Tag', 'hour'=>'Stunde', 'minute'=>'Minute', 'second'=>'Sekunde', 'ago'=>'vor', 'format'=>'{ago} {value} {unit}en'], // simplified
            'fr' => ['year'=>'an', 'month'=>'mois', 'week'=>'semaine', 'day'=>'jour', 'hour'=>'heure', 'minute'=>'minute', 'second'=>'seconde', 'ago'=>'il y a', 'format'=>'{ago} {value} {unit}s'],
            'es' => ['year'=>'año', 'month'=>'mes', 'week'=>'semana', 'day'=>'día', 'hour'=>'hora', 'minute'=>'minuto', 'second'=>'segundo', 'ago'=>'hace', 'format'=>'{ago} {value} {unit}s']
        ];
        if (!isset($translations[$lang])) $lang = 'en';

        foreach ($tokens as $unit => $text) {
            if ($diff < $unit) continue;
            $numberOfUnits = floor($diff / $unit);
            
            // Pluralization logic is basic here, but functional for general use
            $unitStr = $translations[$lang][$text];
            if ($lang === 'en' && $numberOfUnits == 1) $translations['en']['format'] = '{value} {unit} {ago}';
            
            $formatted = str_replace(
                ['{value}', '{unit}', '{ago}'], 
                [$numberOfUnits, $unitStr, $translations[$lang]['ago']], 
                $translations[$lang]['format']
            );
            
            // Fix DE basic plurals roughly
            if ($lang === 'de') {
                $formatted = str_replace(['Jahren', 'Monaten', 'Wocheen', 'Tagen', 'Stundeen', 'Minuteen', 'Sekundeen'], ['Jahren', 'Monaten', 'Wochen', 'Tagen', 'Stunden', 'Minuten', 'Sekunden'], $formatted);
            }
            return self::display($formatted);
        }
        return '-';
    }

    // --- Metin (String) İşleme Fonksiyonları ---

    /**
     * Uzun metinleri keser ve sonuna ... ekler
     */
    public static function truncate(?string $text, int $length = 100, string $append = '...'): string
    {
        if (empty($text)) return '';
        $text = strip_tags($text);
        if (mb_strlen($text, 'UTF-8') <= $length) return self::display($text);
        
        $truncated = mb_substr($text, 0, $length, 'UTF-8');
        // Son kelimeyi bölmemek için son boşluğa kadar al
        if (strpos($text, ' ') !== false) {
            $lastSpace = mb_strrpos($truncated, ' ', 0, 'UTF-8');
            if ($lastSpace !== false) {
                $truncated = mb_substr($truncated, 0, $lastSpace, 'UTF-8');
            }
        }
        return self::display($truncated . $append);
    }

    // --- Para Birimi Formatlama ---

    /**
     * Tutar ve para birimi sembolü formatlama
     */
    public static function money($amount, string $currency = 'usd'): string
    {
        if ($amount === null || $amount === '') return '-';
        $amount = (float)$amount;
        
        $currencies = [
            'usd' => '$',   // US Dollar
            'eur' => '€',   // Euro
            'try' => '₺',   // Turkish Lira
            'tl'  => '₺',   // Turkish Lira
            'gbp' => '£',   // British Pound
            'jpy' => '¥',   // Japanese Yen
            'cny' => '¥',   // Chinese Yuan
            'rub' => '₽',   // Russian Ruble
            'inr' => '₹',   // Indian Rupee
            'aud' => 'A$',  // Australian Dollar
            'cad' => 'C$',  // Canadian Dollar
            'chf' => 'CHF', // Swiss Franc
            'krw' => '₩',   // South Korean Won
            'brl' => 'R$',  // Brazilian Real
            'zar' => 'R',   // South African Rand
            'sek' => 'kr',  // Swedish Krona
            'nok' => 'kr',  // Norwegian Krone
            'mxn' => '$',   // Mexican Peso
            'sgd' => 'S$',  // Singapore Dollar
            'hkd' => 'HK$', // Hong Kong Dollar
            'nzd' => 'NZ$', // New Zealand Dollar
            'aed' => 'د.إ', // UAE Dirham
            'sar' => '﷼',   // Saudi Riyal
            'pln' => 'zł'   // Polish Zloty
        ];
        
        $currency = strtolower($currency);
        $symbol = $currencies[$currency] ?? strtoupper($currency) . ' ';
        
        // Sayıyı 1.250,50 veya 1,250.50 gibi formatlar. Genel standart olarak 1.250,50 (Avrupa/TR)
        // İhtiyaca göre number_format değiştirilebilir. (TR formatı baz alındı)
        $formattedAmount = number_format($amount, 2, ',', '.');
        
        // USD ve GBP gibi birimlerde sembol genelde başa gelir, TL veya EUR'da sona.
        if (in_array($currency, ['usd', 'gbp', 'aud', 'cad', 'sgd', 'hkd', 'nzd', 'mxn', 'brl'])) {
            return self::display($symbol . $formattedAmount);
        }
        
        return self::display($formattedAmount . ' ' . $symbol);
    }

    /**
     * Metni URL dostu bir slug'a çevirir (Örn: "Merhaba Dünya!" -> "merhaba-dunya")
     */
    public static function slugify(string ...$texts): string
    {
        $text = implode('-', $texts);
        $text = mb_strtolower($text, 'UTF-8');
        
        $search = [
            'ç', 'ğ', 'ı', 'ö', 'ş', 'ü', // TR
            'ä', 'ß',                     // DE
            'ñ',                          // ES
            'é', 'è', 'ê', 'ë', 'à', 'á', 'â', 'î', 'ï', 'í', 'ô', 'ó', 'ú', 'û', 'ù', 'æ', 'œ' // FR & ES
        ];
        $replace = [
            'c', 'g', 'i', 'o', 's', 'u',
            'ae', 'ss',
            'n',
            'e', 'e', 'e', 'e', 'a', 'a', 'a', 'i', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'ae', 'oe'
        ];
        
        $text = str_replace($search, $replace, $text);
        $text = preg_replace('/[^a-z0-9\-]/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        return self::display(trim($text, '-'));
    }

    /**
     * Menülerde geçerli sayfa ise belirtilen class'ı döndürür.
     */
    public static function activeClass(?string $path, string $className = 'active'): string
    {
        $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        return ($currentUri === $path) ? self::display($className) : '';
    }

    /**
     * Dosya boyutlarını okunabilir formata çevirir (KB, MB, GB vb.)
     */
    public static function formatSize(int $bytes): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = floor(log($bytes, 1024));
        return self::display(number_format($bytes / pow(1024, $power), 2, ',', '.') . ' ' . $units[$power]);
    }

    /**
     * E-posta adresini KVKK/GDPR kapsamında maskeler (Örn: u***@artilingo.com)
     */
    public static function maskEmail(?string $email): string
    {
        if (empty($email)) return '-';
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) return self::display($email);
        list($first, $last) = explode('@', $email);
        $first = mb_substr($first, 0, 1, 'UTF-8') . str_repeat('*', max(mb_strlen($first, 'UTF-8') - 1, 3));
        return self::display($first . '@' . $last);
    }

    /**
     * Telefon numarasını KVKK kapsamında maskeler (Örn: +90 555 *** ** 67)
     */
    public static function maskPhone(?string $phone): string
    {
        if (empty($phone)) return '-';
        $phoneStr = preg_replace('/[^\d\+]/', '', $phone);
        $len = strlen($phoneStr);
        if ($len < 6) return self::display($phone);
        $start = substr($phoneStr, 0, 3);
        $end = substr($phoneStr, -2);
        $masked = $start . str_repeat('*', max($len - 5, 3)) . $end;
        return self::display($masked);
    }
}

// Geliştiriciler (Özellikle Juniorlar) için kullanımı en kolay global yardımcı fonksiyonlar
if (!function_exists(__NAMESPACE__ . '\\display')) {
    function display($data, string $default = ''): string {
        return \Bin\ViewMethod::display($data, $default);
    }
}

if (!function_exists(__NAMESPACE__ . '\\escapeUrl')) {
    function escapeUrl(?string $url): string {
        return \Bin\ViewMethod::escapeUrl($url);
    }
}

if (!function_exists(__NAMESPACE__ . '\\formatDate')) {
    function formatDate(?string $date, string $format = 'd.m.Y H:i'): string {
        return \Bin\ViewMethod::formatDate($date, $format);
    }
}

if (!function_exists(__NAMESPACE__ . '\\csrfField')) {
    function csrfField(): string {
        return \Bin\ViewMethod::csrfField();
    }
}

if (!function_exists(__NAMESPACE__ . '\\day')) {
    function day($date): string {
        return \Bin\ViewMethod::day($date);
    }
}

if (!function_exists(__NAMESPACE__ . '\\month')) {
    function month($date): string {
        return \Bin\ViewMethod::month($date);
    }
}

if (!function_exists(__NAMESPACE__ . '\\year')) {
    function year($date): string {
        return \Bin\ViewMethod::year($date);
    }
}

if (!function_exists(__NAMESPACE__ . '\\timeOnly')) {
    function timeOnly($date): string {
        return \Bin\ViewMethod::timeOnly($date);
    }
}

if (!function_exists(__NAMESPACE__ . '\\fulldate')) {
    function fulldate($date): string {
        return \Bin\ViewMethod::fulldate($date);
    }
}

if (!function_exists(__NAMESPACE__ . '\\monthName')) {
    function monthName($date, string $lang = 'tr'): string {
        return \Bin\ViewMethod::monthName($date, $lang);
    }
}

if (!function_exists(__NAMESPACE__ . '\\fulldateName')) {
    function fulldateName($date, string $lang = 'tr'): string {
        return \Bin\ViewMethod::fulldateName($date, $lang);
    }
}

if (!function_exists(__NAMESPACE__ . '\\timeAgo')) {
    function timeAgo($date, string $lang = 'tr'): string {
        return \Bin\ViewMethod::timeAgo($date, $lang);
    }
}

if (!function_exists(__NAMESPACE__ . '\\truncate')) {
    function truncate(?string $text, int $length = 100, string $append = '...'): string {
        return \Bin\ViewMethod::truncate($text, $length, $append);
    }
}

if (!function_exists(__NAMESPACE__ . '\\money')) {
    function money($amount, string $currency = 'usd'): string {
        return \Bin\ViewMethod::money($amount, $currency);
    }
}

if (!function_exists(__NAMESPACE__ . '\\slugify')) {
    function slugify(string ...$texts): string {
        return \Bin\ViewMethod::slugify(...$texts);
    }
}

if (!function_exists(__NAMESPACE__ . '\\activeClass')) {
    function activeClass(string $path, string $className = 'active'): string {
        return \Bin\ViewMethod::activeClass($path, $className);
    }
}

if (!function_exists(__NAMESPACE__ . '\\formatSize')) {
    function formatSize(int $bytes): string {
        return \Bin\ViewMethod::formatSize($bytes);
    }
}

if (!function_exists(__NAMESPACE__ . '\\maskEmail')) {
    function maskEmail(?string $email): string {
        return \Bin\ViewMethod::maskEmail($email);
    }
}

if (!function_exists(__NAMESPACE__ . '\\maskPhone')) {
    function maskPhone(?string $phone): string {
        return \Bin\ViewMethod::maskPhone($phone);
    }
}
