<?php
namespace Tests\EntityWire\UnitOfWork;

use EntityWire\UnitOfWork\UnitOfWork;

/**
 * Class UnitOfWorkTest
 * @package Tests\EntityWire\UnitOfWork
 */
class UnitOfWorkTest extends \PHPUnit_Framework_TestCase {

    use DataProvider;
    use MockProvider;

    /**
     * @var \Mockery\MockInterface | \EntityWire\Transaction\TransactionManagerInterface
     */
    private $transactionManager;

    /**
     * @var \Mockery\MockInterface | \EntityWire\Mapper\EntityMapperInterface
     */
    private $entityMapper;

    /**
     * @var UnitOfWork
     */
    private $unitOfWork;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->transactionManager = $this->mockTransactionManager();
        $this->entityMapper = $this->mockEntityMapper();

        $this->unitOfWork = new UnitOfWork($this->transactionManager, $this->entityMapper);
    }

    /**
     * @dataProvider nonObjects
     *
     * @param string $type
     * @param string $value
     * @return void
     */
    public function testNewFailsWhenRegisteringNonObject($type, $value)
    {
        $this->assertFunctionFailsWhenRegisteringNonObject($type, $value, function ($value) {
            $this->unitOfWork->registerNew($value);
        });
    }

    /**
     * @dataProvider singleEntity
     *
     * @param $entity
     * @return void
     */
    public function testNewFailsWhenNoMapperFound($entity)
    {
        $this->assertFunctionFailsWhenNoMapperFound($entity, function($entity) {
            $this->unitOfWork->registerNew($entity);
        });
    }

    /**
     * @dataProvider nonObjects
     *
     * @param string $type
     * @param string $value
     * @return void
     */
    public function testDirtyFailsWhenRegisteringNonObject($type, $value)
    {
        $this->assertFunctionFailsWhenRegisteringNonObject($type, $value, function ($value) {
            $this->unitOfWork->registerDirty($value);
        });
    }

    /**
     * @dataProvider singleEntity
     *
     * @param $entity
     * @return void
     */
    public function testDirtyFailsWhenNoMapperFound($entity)
    {
        $this->assertFunctionFailsWhenNoMapperFound($entity, function($entity) {
            $this->unitOfWork->registerDirty($entity);
        });
    }

    /**
     * @dataProvider nonObjects
     *
     * @param string $type
     * @param string $value
     * @return void
     */
    public function testDeletedFailsWhenRegisteringNonObject($type, $value)
    {
        $this->assertFunctionFailsWhenRegisteringNonObject($type, $value, function ($value) {
            $this->unitOfWork->registerDeleted($value);
        });
    }

    /**
     * @dataProvider singleEntity
     *
     * @param $entity
     * @return void
     */
    public function testDeletedFailsWhenNoMapperFound($entity)
    {
        $this->assertFunctionFailsWhenNoMapperFound($entity, function($entity) {
            $this->unitOfWork->registerDeleted($entity);
        });
    }

    /**
     * @dataProvider multipleEntities
     *
     * @param $newAndInsertedEntity
     * @param $newAndDeletedEntity
     * @return void
     */
    public function testDeleteKeepsNewEntitiesFromInsertion($newAndInsertedEntity, $newAndDeletedEntity)
    {
        $this->entityMapper->shouldReceive('insert')
            ->with($newAndInsertedEntity)
            ->once();

        $this->entityMapper->shouldReceive('insert')
            ->with($newAndDeletedEntity)
            ->never();

        $this->entityMapper->shouldReceive('delete')
            ->with($newAndDeletedEntity)
            ->never();

        $this->unitOfWork->registerNew($newAndInsertedEntity);
        $this->unitOfWork->registerNew($newAndDeletedEntity);

        $this->unitOfWork->registerDeleted($newAndDeletedEntity);

        $this->unitOfWork->commit();
    }


    /**
     * @dataProvider singleEntity
     *
     * @param $newAndDirtyEntity
     */
    public function testDirtySkipsNewEntities($newAndDirtyEntity)
    {
        $this->entityMapper->shouldReceive('insert')
            ->with($newAndDirtyEntity)
            ->once();

        $this->entityMapper->shouldReceive('update')
            ->with($newAndDirtyEntity)
            ->never();

        $this->unitOfWork->registerNew($newAndDirtyEntity);
        $this->unitOfWork->registerDirty($newAndDirtyEntity);

        $this->unitOfWork->commit();
    }

    /**
     * @dataProvider multipleEntities
     *
     * @param $mappedEntity1
     * @param $mappedEntity2
     * @param $mappedEntity3
     * @return void
     */
    public function testCommitInsertsEntitiesIntoMapper($mappedEntity1, $mappedEntity2, $mappedEntity3)
    {
        $mappedEntities = array($mappedEntity1, $mappedEntity2, $mappedEntity3);

        foreach ($mappedEntities as $mappedEntity) {
            $this->entityMapper->shouldReceive('insert')
                ->with($mappedEntity)
                ->once();

            $this->unitOfWork->registerNew($mappedEntity);
        }

        $this->unitOfWork->commit();
    }

    /**
     * @dataProvider multipleEntities
     *
     * @param $mappedEntity1
     * @param $mappedEntity2
     * @param $mappedEntity3
     * @return void
     */
    public function testCommitUpdatesEntitiesInMapper($mappedEntity1, $mappedEntity2, $mappedEntity3)
    {
        $mappedEntities = array($mappedEntity1, $mappedEntity2, $mappedEntity3);

        foreach ($mappedEntities as $mappedEntity) {
            $this->entityMapper->shouldReceive('update')
                ->with($mappedEntity)
                ->once();

            $this->unitOfWork->registerDirty($mappedEntity);
        }

        $this->unitOfWork->commit();
    }

    /**
     * @dataProvider multipleEntities
     *
     * @param $deletedEntity1
     * @param $deletedEntity2
     * @param $deletedEntity3
     * @return void
     */
    public function testCommitDeletesEntitiesFromMapper($deletedEntity1, $deletedEntity2, $deletedEntity3)
    {
        $mappedEntities = array($deletedEntity1, $deletedEntity2, $deletedEntity3);

        foreach ($mappedEntities as $mappedEntity) {
            $this->entityMapper->shouldReceive('delete')
                ->with($mappedEntity)
                ->once();

            $this->unitOfWork->registerDeleted($mappedEntity);
        }

        $this->unitOfWork->commit();
    }

    /**
     * @dataProvider singleEntity
     *
     * @param $entity
     * @return void
     */
    public function testCommitStartsAndClosesTransaction($entity)
    {
        $this->transactionManager->shouldReceive('startTransaction')
            ->once();

        $this->transactionManager->shouldReceive('commitTransaction')
            ->once();

        $this->unitOfWork->registerNew($entity);
        $this->unitOfWork->commit();
    }

    /**
     * @param $entity
     * @param callable $registerMethod
     * @return void
     */
    private function assertFunctionFailsWhenNoMapperFound($entity, callable $registerMethod)
    {
        $this->entityMapper->shouldReceive('hasMapFor')
            ->with($entity)
            ->once()
            ->andReturn(false);

        $entityClass = get_class($entity);

        $this->setExpectedExceptionRegExp(
            'EntityWire\UnitOfWork\Exception\EntityMapperNotFoundException',
            "/$entityClass/"
        );

        $registerMethod($entity);
    }

    /**
     * @param $type
     * @param $value
     * @param callable $registerMethod
     * @return void
     */
    private function assertFunctionFailsWhenRegisteringNonObject($type, $value, callable $registerMethod)
    {
        $this->setExpectedExceptionRegExp(
            'EntityWire\UnitOfWork\Exception\InvalidEntityException',
            "/$type/"
        );

        $registerMethod($value);
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        \Mockery::close();
    }
}
