<?php
namespace App\Enum;

abstract class AccessType extends BasicEnum {
    const View = 'view';
    const Add = 'add';
    const Update = 'edit';
    const Delete = 'delete';
}
