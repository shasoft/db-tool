<?php

namespace Shasoft\DbTool\Driver;

use Shasoft\DbTool\DbToolPdo;

// Дополнительные функции работы с БД MySql
class mysql
{
    // Заключить имя таблицы/колонки в кавычки
    static public function quote(string $name): string
    {
        return '`' . $name . '`';
    }
    // Имя текущей БД
    static public function dbName(\PDO $pdo): string|false
    {
        $rows = DbToolPdo::query($pdo, 'SELECT DATABASE() AS ' . self::quote('name'));
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
        $sql = "SELECT " . self::quote('TABLE_NAME') . " FROM information_schema.tables WHERE " . self::quote('table_schema') . " = " . $pdo->quote(self::dbName($pdo));
        $rows = DbToolPdo::query($pdo, $sql);
        if ($rows) {
            $ret = array_map(function (array $row) {
                return $row['TABLE_NAME'];
            }, $rows);
        }
        //
        return $ret;
    }
};
