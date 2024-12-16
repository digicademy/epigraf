<?php

namespace App\Database\Type;

use Exception;

/**
 * Add support for marshalling array values to JSON.
 */
class JsonType extends \Cake\Database\Type\JsonType
{

    /**
     * Marshals request data into a JSON compatible structure.
     *
     * @param mixed $value The value to convert.
     * @return mixed Converted value.
     */
    public function marshal($value)
    {
        if (!empty($value) && !is_array($value)) {
            try {
                $value = json_decode($value ?? '', true, 512, JSON_THROW_ON_ERROR);
                return $value;
            } catch (Exception $e) {
                return $value;
            }

        }
        return $value;
    }

}
