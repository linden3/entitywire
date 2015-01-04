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
        $this->setExpectedExceptionRegExp(
            'EntityWire\UnitOfWork\Exception\InvalidEntityException',
            "/$type/"
        );

        $this->unitOfWork->registerNew($value);
    }

    /**
     * @dataProvider singleEntity
     *
     * @param $unmappedEntity
     * @return void
     */
    public function testNewFailsWhenNoMapperFound($unmappedEntity)
    {
        $this->entityMapper->shouldReceive('hasMapFor')
            ->with($unmappedEntity)
            ->once()
            ->andReturn(false);

        $this->setExpectedException('EntityWire\UnitOfWork\Exception\EntityMapperNotFoundException');

        $this->unitOfWork->registerNew($unmappedEntity);
    }

    /**
     * @dataProvider multipleEntities
     *
     * @param $newAndInsertedEntity
     * @param $newAndDeletedEntity
     * @return void
     */
    public function testCommitPropagatesAdditionToMapper(array $mappedEntities)
    {
    /**
     * @dataProvider multipleEntities
     *
     * @param $mappedEntity1
     * @param $mappedEntity2
     * @param $mappedEntity3
     * @throws \EntityWire\UnitOfWork\Exception\EntityMapperNotFoundException
     * @throws \EntityWire\UnitOfWork\Exception\InvalidEntityException
     */
    public function testCommitPropagatesAdditionToMapper($mappedEntity1, $mappedEntity2, $mappedEntity3)
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
     * @param $deletedEntity1
     * @param $deletedEntity2
     * @param $deletedEntity3
     * @throws \EntityWire\UnitOfWork\Exception\EntityMapperNotFoundException
     * @throws \EntityWire\UnitOfWork\Exception\InvalidEntityException
     */
    public function testCommitPropagatesDeletionToMapper($deletedEntity1, $deletedEntity2, $deletedEntity3)
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
     * @return void
     */
    public function tearDown()
    {
        \Mockery::close();
    }
}
