<?php
namespace EntityWire\UnitOfWork\Exception;

use Exception;

class InvalidEntityException extends Exception
{
    public function __construct($entity)
    {
        parent::__construct("Expected entity to be an object, got " . gettype($entity));
    }
}
