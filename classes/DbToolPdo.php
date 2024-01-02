<?php

namespace Shasoft\DbTool;

// Расширенные функции работы с PDO
class DbToolPdo
{
    // Вызвать функцию драйвера
    static protected function call(\PDO $pdo, string $name, ...$args): mixed
    {
        return call_user_func(__NAMESPACE__ . '\\Driver\\' . $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) . '::' . $name, ...$args);
    }
    // Заключить имя таблицы/колонки в кавычки
    static public function quote(\PDO $pdo, string $name): string
    {
        return self::call($pdo, 'quote', $name);
    }
    // Выполнить запрос и получить результат
    static public function query(\PDO $pdo, string $sql): array|false
    {
        $ret = false;
        $query = $pdo->query($sql);
        if ($query) {
            $ret = $query->fetchAll(\PDO::FETCH_ASSOC);
        }
        return $ret;
    }
    // Имя текущей БД
    static public function dbName(\PDO $pdo): string
    {
        return self::call($pdo, 'dbName', $pdo);
    }
    // Список таблиц
    static public function tables(\PDO $pdo): array
    {
        return self::call($pdo, 'tables', $pdo);
    }
    // Очистить БД (т.не. удалить все таблицы с данными)
    static public function reset(\PDO $pdo): void
    {
        foreach (self::tables($pdo) as $tabname) {
            self::query($pdo, 'DROP TABLE IF EXISTS ' . self::quote($pdo, $tabname));
        }
    }
};
