<?php
namespace App\AuthValidators;

use App\Enum\AccessType;
use App\Enum\ResourceType;
use App\Exceptions\NoPrivilageException;

class ManufacturerValidator
{
    public static function viewValidator()
    {
        if (!AuthValidator::isAdmin()
            && !AuthValidator::isPrivileged(ResourceType::Manufacturer, AccessType::View)) {

            throw new NoPrivilageException(['View privilage not found!']);
        }
    }

    public static function storeValidator()
    {
        if (!AuthValidator::isAdmin()
            && !AuthValidator::isPrivileged(ResourceType::Manufacturer, AccessType::Add)) {

            throw new NoPrivilageException(['Create privilage not found!']);
        }
    }

    public static function updateValidator()
    {
        if (!AuthValidator::isAdmin()
            && !AuthValidator::isPrivileged(ResourceType::Manufacturer, AccessType::Update)) {

            throw new NoPrivilageException(['Update privilage not found!']);
        }
    }

    public static function deleteValidator()
    {
        if (!AuthValidator::isAdmin()
            && !AuthValidator::isPrivileged(ResourceType::Manufacturer, AccessType::Delete)) {

            throw new NoPrivilageException(['Delete privilage not found!']);
        }
    }
}
