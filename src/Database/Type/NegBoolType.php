<?php

namespace App\Database\Type;

use Cake\Database\DriverInterface;
use Cake\Database\Type\BaseType;
use PDO;

/**
 * Boolean type with -1 = true and 0 = false
 *
 * EpiDesktop saves some true values (e.g. in the file_online field) as -1.
 */
class NegBoolType extends BaseType
{

    /**
     * Convert value to a PHP boolean
     *
     * @param $value
     * @param DriverInterface $driver
     *
     * @return bool|null
     */
    public function toPHP($value, DriverInterface $driver): ?bool
    {
        if ($value === null) {
            return null;
        }
        return !empty($value);
    }

    /**
     * Marshals flat data into PHP objects
     *
     * Most useful for converting request data into PHP objects
     * that make sense for the rest of the ORM/Database layers.
     *
     * @param $value
     *
     * @return mixed
     */
    public function marshal($value)
    {
        return $value;
    }

    /**
     * Convert data into the database format.
     *
     * @param $value
     * @param DriverInterface $driver
     *
     * @return int|null
     */
    public function toDatabase($value, DriverInterface $driver)
    {
        if ($value === null) {
            return null;
        }
        else {
            return $value ? -1 : 0;
        }
    }

    /**
     * Get the correct PDO binding type
     *
     * @param mixed $value The value being bound.
     * @param \Cake\Database\DriverInterface $driver The driver.
     *
     * @return int|mixed
     */
    public function toStatement($value, DriverInterface $driver)
    {
        if ($value === null) {
            return PDO::PARAM_NULL;
        }
        return PDO::PARAM_INT;
    }

}
