<?php

namespace EthicalJobs\Elasticsearch;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Static utility class
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */
class Utilities
{
    /**
     * Returns response errors
     *
     * @param array $response
     * @return array
     */
    public static function getResponseErrors(array $response): array
    {
        if (!static::isResponseValid($response)) {
            return $response['items'];
        }

        return [];
    }

    /**
     * Validates an Elasicsearch API response
     *
     * @param array $response
     * @return bool
     */
    public static function isResponseValid(array $response): bool
    {
        if (isset($response['errors']) && $response['errors']) {
            return false;
        }

        return true;
    }

    /**
     * Determine if model is soft deletable
     *
     * @param mixed $entity
     * @return bool
     */
    public static function isSoftDeletable($entity): bool
    {
        return in_array(
            SoftDeletes::class,
            class_uses($entity)
        );
    }

    /**
     * Translates SQL like operators to ES dsl operators
     *
     * @param string $operator
     * @return string
     */
    public static function translateOperator(string $operator): string
    {
        switch ($operator) {
            case '<=':
                return 'lte';
            case '>=':
                return 'gte';
            case '<':
                return 'lt';
            case '>':
                return 'gt';
        }

        return $operator;
    }

    /**
     * Truth test if entity is indaxable
     *
     * @param Model $entity
     * @return bool
     */
    public static function isIndexable(Model $entity): bool
    {
        if (in_array(get_class($entity), static::getIndexables())) {
            return true;
        }

        return false;
    }

    /**
     * Returns the indexable entities
     *
     * @return array
     */
    public static function getIndexables(): array
    {
        return static::config('indexables', []);
    }

    /**
     * Returns config setting(s)
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function config(string $key = '', $default = null)
    {
        $key = (empty($key) === true) ? '' : ".$key"; // Allow empty key

        return config("elasticsearch$key", $default);
    }
}