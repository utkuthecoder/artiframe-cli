<?php
/**
 * ArtiFrame Database Mini-ORM & SQL Helpers
 *
 * @package     ArtiFrame
 * @author      Artilingo
 * @license     AGPLv3
 */

namespace Bin {

class SQLMethod
{
    /**
     * Güvenli ve hızlı bir PDO Prepare & Execute sarmalayıcısı.
     */
    public static function executeStmt(string $sql, array $params = []): \PDOStatement
    {
        // App\Database PSR-4 ile otomatik yüklenir. Sadece kullanıldığı yerde devreye girer.
        $db = \App\Database::getInstance();
        \App\Database::ping(); 
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt;
    }

    public static function dbInsert(string $table, array $data): string
    {
        if (empty($data)) {
            throw new \InvalidArgumentException("Eklenecek veri (data) boş olamaz.");
        }

        // Tablo adını güvenlik için ters tırnak (backtick) ile saralım
        $table = "`" . preg_replace('/[^a-zA-Z0-9_]/', '', $table) . "`";

        $columns = [];
        $placeholders = [];
        $params = [];

        foreach ($data as $key => $value) {
            $columns[] = "`" . preg_replace('/[^a-zA-Z0-9_]/', '', $key) . "`";
            $placeholders[] = "?";
            $params[] = $value;
        }

        $sql = "INSERT INTO {$table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        self::executeStmt($sql, $params);
        
        return \App\Database::getInstance()->lastInsertId();
    }

    public static function dbUpdate(string $table, array $data, array $where): int
    {
        if (empty($data)) {
            throw new \InvalidArgumentException("Güncellenecek veri (data) boş olamaz.");
        }
        if (empty($where)) {
            throw new \InvalidArgumentException("BOMBA RİSKİ: Neye göre güncelleneceği (where) boş olamaz! Tüm tabloyu güncelleyemezsiniz.");
        }

        $table = "`" . preg_replace('/[^a-zA-Z0-9_]/', '', $table) . "`";

        $setParts = [];
        $params = [];
        foreach ($data as $key => $value) {
            $setParts[] = "`" . preg_replace('/[^a-zA-Z0-9_]/', '', $key) . "` = ?";
            $params[] = $value;
        }

        [$whereParts, $whereParams] = self::parseWhere($where);
        $params = array_merge($params, $whereParams);

        $sql = "UPDATE {$table} SET " . implode(', ', $setParts) . " WHERE " . implode(' AND ', $whereParts);
        $stmt = self::executeStmt($sql, $params);
        
        return $stmt->rowCount();
    }

    public static function dbDelete(string $table, array $where): int
    {
        if (empty($where)) {
            throw new \InvalidArgumentException("BOMBA RİSKİ: Neye göre silineceği (where) boş olamaz! Tüm tabloyu silemezsiniz.");
        }

        $table = "`" . preg_replace('/[^a-zA-Z0-9_]/', '', $table) . "`";

        [$whereParts, $params] = self::parseWhere($where);

        $sql = "DELETE FROM {$table} WHERE " . implode(' AND ', $whereParts);
        $stmt = self::executeStmt($sql, $params);
        
        return $stmt->rowCount();
    }

    
    /**
     * WHERE dizisini güvenli SQL ve Parametrelere çevirir.
     */
    private static function parseWhere(array $where): array
    {
        $whereParts = [];
        $params = [];
        
        foreach ($where as $key => $value) {
            $key = trim($key);
            // İçinde operatör varsa (Örn: "yas >" veya "isim LIKE")
            if (preg_match('/^([a-zA-Z0-9_\.]+)\s*(>=|<=|!=|<>|>|<|LIKE|NOT LIKE)$/i', $key, $matches)) {
                $column = $matches[1];
                $operator = strtoupper($matches[2]);
            } else {
                $column = preg_replace('/[^a-zA-Z0-9_\.]/', '', $key);
                $operator = '=';
            }
            
            // Tabloadi.Sutun formatını güvenli backtick içine al: `tabloadi`.`sutun`
            $colParts = explode('.', $column);
            $safeCol = implode('.', array_map(fn($p) => "`$p`", $colParts));
            
            $whereParts[] = "{$safeCol} {$operator} ?";
            $params[] = $value;
        }
        
        return [$whereParts, $params];
    }

    public static function dbGet(string $table, array $where = [], string $orderBy = ''): mixed
    {
        $table = "`" . preg_replace('/[^a-zA-Z0-9_]/', '', $table) . "`";
        
        $sql = "SELECT * FROM {$table}";
        $params = [];
        
        if (!empty($where)) {
            [$whereParts, $params] = self::parseWhere($where);
            $sql .= " WHERE " . implode(' AND ', $whereParts);
        }
        
        if (!empty($orderBy)) {
            // Sadece a-z, boşluk ve virgüle izin ver (Injection koruması)
            $orderBy = preg_replace('/[^a-zA-Z0-9_ \.,]/', '', $orderBy);
            $sql .= " ORDER BY {$orderBy}";
        }
        
        $sql .= " LIMIT 1";
        
        return self::executeStmt($sql, $params)->fetch(\PDO::FETCH_ASSOC);
    }

    public static function dbList(string $table, array $where = [], string $orderBy = '', string|int $limit = ''): array
    {
        $table = "`" . preg_replace('/[^a-zA-Z0-9_]/', '', $table) . "`";
        
        $sql = "SELECT * FROM {$table}";
        $params = [];
        
        if (!empty($where)) {
            [$whereParts, $params] = self::parseWhere($where);
            $sql .= " WHERE " . implode(' AND ', $whereParts);
        }
        
        if (!empty($orderBy)) {
            $orderBy = preg_replace('/[^a-zA-Z0-9_ \.,]/', '', $orderBy);
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if (!empty($limit)) {
            // Sadece rakam ve virgüle izin ver (Örn: "0, 20" veya "20")
            $limit = preg_replace('/[^0-9, ]/', '', (string)$limit);
            $sql .= " LIMIT {$limit}";
        }
        
        return self::executeStmt($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function dbCount(string $table, array $where = []): int
    {
        $table = "`" . preg_replace('/[^a-zA-Z0-9_]/', '', $table) . "`";
        
        $sql = "SELECT COUNT(*) FROM {$table}";
        $params = [];
        
        if (!empty($where)) {
            [$whereParts, $params] = self::parseWhere($where);
            $sql .= " WHERE " . implode(' AND ', $whereParts);
        }
        
        return (int) self::executeStmt($sql, $params)->fetchColumn(0);
    }

    public static function dbRow(string $sql, array $params = [], int $fetchStyle = \PDO::FETCH_ASSOC): mixed
    {
        return self::executeStmt($sql, $params)->fetch($fetchStyle);
    }

    public static function dbRows(string $sql, array $params = [], int $fetchStyle = \PDO::FETCH_ASSOC): array
    {
        return self::executeStmt($sql, $params)->fetchAll($fetchStyle);
    }

    public static function dbValue(string $sql, array $params = [], int $column = 0): mixed
    {
        return self::executeStmt($sql, $params)->fetchColumn($column);
    }
}
}


namespace {
    if (!function_exists('dbExecute')) {
        function dbExecute(string $sql, array $params = []): \PDOStatement {
            return \Bin\SQLMethod::executeStmt($sql, $params);
        }
    }
    if (!function_exists('dbInsert')) {
        function dbInsert(string $table, array $data): string {
            return \Bin\SQLMethod::dbInsert($table, $data);
        }
    }
    if (!function_exists('dbUpdate')) {
        function dbUpdate(string $table, array $data, array $where): int {
            return \Bin\SQLMethod::dbUpdate($table, $data, $where);
        }
    }
    if (!function_exists('dbDelete')) {
        function dbDelete(string $table, array $where): int {
            return \Bin\SQLMethod::dbDelete($table, $where);
        }
    }
        if (!function_exists('dbGet')) {
        function dbGet(string $table, array $where = [], string $orderBy = ''): mixed {
            return \Bin\SQLMethod::dbGet($table, $where, $orderBy);
        }
    }
    if (!function_exists('dbList')) {
        function dbList(string $table, array $where = [], string $orderBy = '', string|int $limit = ''): array {
            return \Bin\SQLMethod::dbList($table, $where, $orderBy, $limit);
        }
    }
    if (!function_exists('dbCount')) {
        function dbCount(string $table, array $where = []): int {
            return \Bin\SQLMethod::dbCount($table, $where);
        }
    }
    if (!function_exists('dbRow')) {
        function dbRow(string $sql, array $params = [], int $fetchStyle = \PDO::FETCH_ASSOC): mixed {
            return \Bin\SQLMethod::dbRow($sql, $params, $fetchStyle);
        }
    }
    if (!function_exists('dbRows')) {
        function dbRows(string $sql, array $params = [], int $fetchStyle = \PDO::FETCH_ASSOC): array {
            return \Bin\SQLMethod::dbRows($sql, $params, $fetchStyle);
        }
    }
    if (!function_exists('dbValue')) {
        function dbValue(string $sql, array $params = [], int $column = 0): mixed {
            return \Bin\SQLMethod::dbValue($sql, $params, $column);
        }
    }
}
