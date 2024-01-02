<?php

namespace Shasoft\DbTool\Driver;

use Shasoft\DbTool\DbToolPdo;

// Дополнительные функции работы с БД PostgresSql
class pgsql
{
    // Заключить имя таблицы/колонки в кавычки
    static public function quote(string $name): string
    {
        return '"' . $name . '"';
    }
    // Имя текущей БД
    static public function dbName(\PDO $pdo): string|false
    {
        $rows = DbToolPdo::query($pdo, 'SELECT current_database() AS ' . self::quote('name'));
        if ($rows && !empty($rows)) {
            return $rows[0]['name'];
        }
        return false;
    }
    // Список таблиц
    static public function tables(\PDO $pdo): array
    {
        $ret = [];
        // Получить список таблиц БД
        $sql = "SELECT " . self::quote('table_name') . " FROM information_schema.tables WHERE " .
            self::quote('table_catalog') . " = " . $pdo->quote(self::dbName($pdo)) .
            " AND " .
            self::quote('table_schema') . " = 'public'";
        // SELECT "table_name" FROM information_schema.tables WHERE "table_catalog" = 'resumes' AND "table_schema" = 'public'
        $rows = DbToolPdo::query($pdo, $sql);
        if ($rows) {
            $ret = array_map(function (array $row) {
                return $row['table_name'];
            }, $rows);
        }
        //
        return $ret;
    }
};
