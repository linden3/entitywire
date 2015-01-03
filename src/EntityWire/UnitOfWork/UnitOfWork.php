<?php
namespace EntityWire\UnitOfWork;

use EntityWire\Mapper\MapperInterface as EntityMapper;
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
    private $entityMapper;

    /**
     * @var mixed
     */
    private $entities;

    /**
     * @param EntityMapper $entityMapper
     */
    function __construct(EntityMapper $entityMapper)
    {
        $this->entityMapper = $entityMapper;
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

        if (! $this->entityMapper->mapsEntity($entity)) {
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
            $this->entityMapper->insert($entity);
        }
    }
}
