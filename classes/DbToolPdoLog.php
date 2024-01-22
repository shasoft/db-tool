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
    static public function write(\PDOStatement $sth, ?\PDO $pdo = null, ?array $params = null): void
    {
        if (self::$on) {
            // Вывести информацию о запросе
            ob_start();
            $sth->debugDumpParams();
            $text = ob_get_contents();
            ob_end_clean();
            //
            $marker = "\nParams:";
            $pos = strrpos($text, $marker);
            $cnt = intval(ltrim(substr($text, $pos + strlen($marker))));
            // оставить SQL
            $sql = substr($text, 0, $pos);
            //s_dump($pos, $text, $cnt);
            // В зависимости от количества параметров
            if ($cnt == 0) {
                $pos = strpos($sql, ']');
                $sql = substr($sql, $pos + 1);
            } else {
                $pos = strpos($sql, 'Sent SQL: [');
                if ($pos !== false) {
                    $pos = strpos($sql, ']', $pos);
                    $sql = substr($sql, $pos + 1);
                } else {
                    $pos = strpos($sql, ']');
                    $sql = substr($sql, $pos + 1);
                    if (!is_null($pdo) && !is_null($params)) {
                        $tmp = explode('Key: Name:', $text);
                        array_shift($tmp);
                        foreach ($tmp as $str) {
                            $tmpItem = explode("\n", $str);
                            array_shift($tmpItem);
                            $pValues = [];
                            foreach ($tmpItem as $line) {
                                $line = trim($line);
                                if (!empty($line)) {
                                    $tmpLine = explode('=', $line);
                                    if ($tmpLine[0] == 'name') {
                                        $tmpLine[1] = substr($tmpLine[1], strpos($tmpLine[1], ':') + 1);
                                        $tmpLine[1] = str_replace('"', '', $tmpLine[1]);
                                    }
                                    $pValues[$tmpLine[0]] = $tmpLine[1];
                                }
                            }
                            $name = $pValues['name'];
                            $type = intval($pValues['param_type']);
                            //
                            if ($type == \PDO::PARAM_INT) {
                                $repValue = $params[$name];
                            } else if ($type == \PDO::PARAM_NULL) {
                                $repValue = 'NULL';
                            } else {
                                $repValue = $pdo->quote($params[$name], $type);
                            }
                            // Заменить значение
                            $sql = str_replace(':' . $name, $repValue, $sql);
                        }
                    }
                }
            }
            // Заменить все непечатные символы на тильду 
            //$sql = preg_replace('/[[:^print:]]/', '~', $sql);
            $sql = mb_ereg_replace('/[[:^print:]]/', '~', $sql);
            // SQL запрос
            self::$sqlLog[] = trim($sql);
            /*
        $parser = new Parser($text);
        $flags = PdoQuery::getFlags($parser->statements[0]);
        s_dump($flags, $parser);
        //*/
        }
    }
}
