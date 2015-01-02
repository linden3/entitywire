<?php
namespace EntityWire\UnitOfWork\Exception;

use Exception;

class EntityMapperNotFoundException extends Exception
{
    public function __construct($entity)
    {
        parent::__construct("Mapper not found for entity of class " . get_class($entity));
    }
}
