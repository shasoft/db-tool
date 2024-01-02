<?php

namespace Shasoft\DbTool;

use Shasoft\DbTool\DbToolSqlFormat;

// Логирование PDO запросов
class DbToolPdoLog
{
    // Режим работы
    static protected bool $on = true;
    // Все команды
    static protected array $sqlLog = [];
    // Установить режим работы
    static public function setMode(bool $value): void
    {
        self::$on = $value;
    }
    // Очистить лог запросов
    static public function clear(): void
    {
        self::$sqlLog = [];
    }
    // Получить список запросов
    static public function getRaw(): array
    {
        return self::$sqlLog;
    }
    // Получить данные лога
    static public function getLog(): string
    {
        $ret = '';
        foreach (self::$sqlLog as $sql) {
            $ret .= DbToolSqlFormat::auto($sql);
        }
        return $ret;
    }
    // Записать в лог информацию о ВЫПОЛНЕННОМ запросе
    static public function write(\PDOStatement $sth): void
    {
        if (self::$on) {
            // Вывести информацию о запросе
            ob_start();
            $sth->debugDumpParams();
            $text = ob_get_contents();
            ob_end_clean();
            //s_dump($text, $this->sth->queryString);
            //
            $marker = "\nParams:";
            $pos = strrpos($text, $marker);
            $cnt = intval(ltrim(substr($text, $pos + strlen($marker))));
            // оставить SQL
            $text = substr($text, 0, $pos);
            //s_dump($pos, $text);
            // В зависимости от количества параметров
            if ($cnt == 0) {
                $pos = strpos($text, ']');
                $text = substr($text, $pos + 1);
            } else {
                $pos = strpos($text, 'Sent SQL: [');
                $pos = strpos($text, ']', $pos);
                $text = substr($text, $pos + 1);
            }
            // Заменить все непечатные символы на тильду 
            $text = preg_replace('/[[:^print:]]/', '~', $text);
            // SQL запрос
            self::$sqlLog[] = trim($text);
            /*
        $parser = new Parser($text);
        $flags = PdoQuery::getFlags($parser->statements[0]);
        s_dump($flags, $parser);
        //*/
        }
    }
}
