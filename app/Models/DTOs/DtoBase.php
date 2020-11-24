<?php

namespace App\Models\DTOs;

class DtoBase
{
    public function toArray()
    {
        return (array) $this;
    }
}
