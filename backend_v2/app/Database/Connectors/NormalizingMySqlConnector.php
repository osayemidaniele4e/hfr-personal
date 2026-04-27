<?php

namespace App\Database\Connectors;

use Illuminate\Database\Connectors\MySqlConnector;
use PDO;

/**
 * DATABASE_URL query strings (e.g. from host dashboards) can merge into config as options[20]=false,
 * leaving string values for PDO attributes that require real booleans — PHP 8+ then throws from PDO::__construct.
 */
class NormalizingMySqlConnector extends MySqlConnector
{
    public function getOptions(array $config)
    {
        return $this->normalizePdoOptions(parent::getOptions($config));
    }

    /**
     * @param  array<int|string, mixed>  $options
     * @return array<int, mixed>
     */
    private function normalizePdoOptions(array $options): array
    {
        $boolIds = self::booleanPdoAttributeIds();
        $out = [];

        foreach ($options as $key => $value) {
            $k = is_int($key) ? $key : (is_string($key) && ctype_digit($key) ? (int) $key : null);
            if ($k === null) {
                continue;
            }

            if (in_array($k, $boolIds, true) && is_string($value)) {
                $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                $value = $filtered ?? false;
            }

            $out[$k] = $value;
        }

        return $out;
    }

    /**
     * @return list<int>
     */
    private static function booleanPdoAttributeIds(): array
    {
        static $ids;

        if ($ids !== null) {
            return $ids;
        }

        $ids = [
            PDO::ATTR_PERSISTENT,
            PDO::ATTR_STRINGIFY_FETCHES,
            PDO::ATTR_EMULATE_PREPARES,
        ];

        foreach ([
            'MYSQL_ATTR_USE_BUFFERED_QUERY',
            'MYSQL_ATTR_LOCAL_INFILE',
            'MYSQL_ATTR_COMPRESS',
            'MYSQL_ATTR_DIRECT_QUERY',
            'MYSQL_ATTR_FOUND_ROWS',
            'MYSQL_ATTR_IGNORE_SPACE',
            'MYSQL_ATTR_SSL_VERIFY_SERVER_CERT',
            'MYSQL_ATTR_MULTI_STATEMENTS',
        ] as $name) {
            if (defined('PDO::'.$name)) {
                $ids[] = constant('PDO::'.$name);
            }
        }

        return $ids;
    }
}
