<?php
namespace App\Enum;

abstract class ResourceType extends BasicEnum {
    const Group = 'group';
    const User = 'user';
    const Customer = 'customer';
    const Role = 'role';
    const Device = 'device';
}
