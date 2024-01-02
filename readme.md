## Дополнительные инструменты для работы с БД


```php
// Расширенные функции работы с PDO
class DbToolPdo
{
    // Заключить имя таблицы/колонки в кавычки
    static public function quote(\PDO $pdo, string $name): string;
    // Выполнить запрос и получить результат
    static public function query(\PDO $pdo, string $sql): array|false;
    // Имя текущей БД
    static public function dbName(\PDO $pdo): string;
    // Список таблиц
    static protected function tables(\PDO $pdo): array;
    // Очистить БД (т.не. удалить все таблицы с данными)
    static public function reset(\PDO $pdo): void;
};
```

```php
// Логирование PDO запросов
class DbToolPdoLog
{
    // Установить режим работы
    static public function setMode(bool $value): void;
    // Очистить лог запросов
    static public function clear(): void;
    // Получить список запросов
    static public function getRaw(): array;
    // Получить данные лога
    static public function getLog(): string;
    // Записать в лог информацию о ВЫПОЛНЕННОМ запросе
    static public function write(\PDOStatement $sth): void;
}
```

```php
// Форматирование SQL
class DbToolSqlFormat
{
    // Форматирование SQL для вывода в зависимости от режима работы
    static public function auto(string $sql): string;
    // Форматирование SQL для вывода в html
    static public function html(string $sql): string;
    // Форматирование SQL для консоли
    static public function console(string $sql): string;
};
```