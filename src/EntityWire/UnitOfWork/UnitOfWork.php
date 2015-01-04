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
     * @var EntityMapper
     */
    private $entityMapper;

    /**
     * @var array
     */
    private $newEntities = array();

    /**
     * @var array
     */
    private $deletedEntities = array();

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

        $this->newEntities[] = $entity;
    }

    /**
     * @param mixed $entity
     * @throws EntityMapperNotFoundException
     * @throws InvalidEntityException
     */
    public function registerDeleted($entity)
    {
        if (! is_object($entity)) {
            throw new InvalidEntityException($entity);
        }

        if (! $this->entityMapper->hasMapFor($entity)) {
            throw new EntityMapperNotFoundException($entity);
        }

        $this->deletedEntities[] = $entity;
    }

    /**
     *
     */
    public function commit()
    {
        $this->transactionManager->startTransaction();

        foreach ($this->newEntities as $entity) {
            $this->entityMapper->insert($entity);
        }

        foreach ($this->deletedEntities as $entity) {
            $this->entityMapper->delete($entity);
        }

        $this->transactionManager->commitTransaction();
    }
}
