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
     * @var array
     */
    private $dirtyEntities = array();

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
        $this->guardSuitabilityOfEntity($entity);

        $this->newEntities[] = $entity;
    }

    /**
     * @param mixed $entity
     * @throws EntityMapperNotFoundException
     * @throws InvalidEntityException
     * @return void
     */
    public function registerDirty($entity)
    {
        $this->guardSuitabilityOfEntity($entity);

        if ($this->isRegisteredAsNew($entity)) {
            // If the entity is added and then immediately marked dirty, it is still new, so no action is required.
            return;
        }

        $this->dirtyEntities[] = $entity;
    }

    /**
     * @param mixed $entity
     * @throws EntityMapperNotFoundException
     * @throws InvalidEntityException
     * @return void
     */
    public function registerDeleted($entity)
    {
        $this->guardSuitabilityOfEntity($entity);

        if ($this->isRegisteredAsNew($entity)) {
            // If the entity is added and then immediately deleted, it does not need to be added to or deleted from the
            // mapper. This means that it should be removed from the new entities and not added to the deleted entities.
            unset($this->newEntities[array_search($entity, $this->newEntities)]);
            return;
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

        foreach ($this->dirtyEntities as $entity) {
            $this->entityMapper->update($entity);
        }

        foreach ($this->deletedEntities as $entity) {
            $this->entityMapper->delete($entity);
        }

        $this->transactionManager->commitTransaction();
    }

    /**
     * @param $entity
     * @throws EntityMapperNotFoundException
     * @throws InvalidEntityException
     */
    private function guardSuitabilityOfEntity($entity)
    {
        if (!is_object($entity)) {
            throw new InvalidEntityException($entity);
        }

        if (!$this->entityMapper->hasMapFor($entity)) {
            throw new EntityMapperNotFoundException($entity);
        }
    }

    /**
     * @param $entity
     * @return bool
     */
    private function isRegisteredAsNew($entity)
    {
        return in_array($entity, $this->newEntities);
    }
}
