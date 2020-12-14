<?php

namespace App\Services;

class ServiceBase
{
    protected function snakeCase(array $array): array
    {
        return array_map(
            function($item) {
                if (is_array($item)) {
                    $item = $this->snakeCase($item);
                }

                return $item;
            },
            $this->doSnakeCase($array)
        );
    }

    private function doSnakeCase(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $key = strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $key));

            $result[$key] = $value;
        }

        return $result;
    }
}
