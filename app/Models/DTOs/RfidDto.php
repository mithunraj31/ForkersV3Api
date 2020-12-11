<?php


namespace App\Models\DTOs;

use PhpParser\Node\Expr\List_;

class RfidDto extends DtoBase
{
    public $rfid;

    public $rfidName;

    public $createdBy;
}
