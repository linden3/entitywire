<?php
namespace EntityWire\UnitOfWork;

use EntityWire\Mapper\RegistryInterface as MapperRegistry;
use EntityWire\UnitOfWork\Exception\EntityMapperNotFoundException;
use EntityWire\UnitOfWork\Exception\InvalidEntityException;

/**
 * Class UnitOfWork
 * @package EntityWire\UnitOfWork
 */
class UnitOfWork
{
    /**
     * @var MapperRegistry
     */
    private $mapperRegistry;

    /**
     * @var mixed
     */
    private $entities;

    /**
     * @param MapperRegistry $mapperRegistry
     */
    function __construct(MapperRegistry $mapperRegistry)
    {
        $this->mapperRegistry = $mapperRegistry;
    }

    /**
     * @param mixed $entity
     * @throws EntityMapperNotFoundException
     * @throws InvalidEntityException
     */
    public function registerNew($entity)
    {
        if (! is_object($entity)) {
            throw new InvalidEntityException($entity);
        }

        if (! $this->mapperRegistry->hasMapperForEntity($entity)) {
            throw new EntityMapperNotFoundException($entity);
        }

        $this->entities[] = $entity;
    }

    /**
     *
     */
    public function commit()
    {
        foreach ($this->entities as $entity) {
            $mapper = $this->mapperRegistry->getMapperForEntity($entity);

            $mapper->insert($entity);
        }
    }
}
