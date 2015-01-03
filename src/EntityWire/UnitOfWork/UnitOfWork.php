<?php
namespace EntityWire\UnitOfWork;

use EntityWire\Mapper\EntityMapperInterface as EntityMapper;
use EntityWire\Transaction\TransactionManagerInterface as TransactionManager;
use EntityWire\UnitOfWork\Exception\EntityMapperNotFoundException;
use EntityWire\UnitOfWork\Exception\InvalidEntityException;

/**
 * Class UnitOfWork
 * @package EntityWire\UnitOfWork
 */
class UnitOfWork
{
    /**
     * @var TransactionManager
     */
    private $transactionManager;

    /**
     * @var MapperRegistry
     */
    private $entityMapper;

    /**
     * @var mixed
     */
    private $entities;

    /**
     * @param TransactionManager $transactionManager
     * @param EntityMapper $entityMapper
     */
    function __construct(TransactionManager $transactionManager, EntityMapper $entityMapper)
    {
        $this->transactionManager = $transactionManager;
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

        if (! $this->entityMapper->hasMapFor($entity)) {
            throw new EntityMapperNotFoundException($entity);
        }

        $this->entities[] = $entity;
    }

    /**
     *
     */
    public function commit()
    {
        $this->transactionManager->startTransaction();

        foreach ($this->entities as $entity) {
            $this->entityMapper->insert($entity);
        }

        $this->transactionManager->commitTransaction();
    }
}
