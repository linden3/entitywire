<?php
namespace Tests\EntityWire\UnitOfWork;

use EntityWire\UnitOfWork\UnitOfWork;

/**
 * Class UnitOfWorkTest
 * @package Tests\EntityWire\UnitOfWork
 */
class UnitOfWorkTest extends \PHPUnit_Framework_TestCase {

    use DataProviders;

    /**
     * @var UnitOfWork
     */
    private $unitOfWork;

    /**
     * @var \Mockery\MockInterface | \EntityWire\Mapper\RegistryInterface
     */
    private $mapperRegistry;

    /**
     * @var \Mockery\MockInterface
     */
    private $mapper;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->mapperRegistry = \Mockery::mock('\EntityWire\Mapper\RegistryInterface');

        $this->unitOfWork = new UnitOfWork($this->mapperRegistry);

        $this->mapper = \Mockery::mock('Mapper');
    }

    /**
     * @dataProvider nonObjectProvider
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
     * @dataProvider mapperlessEntityProvider
     *
     * @param $mapperlessEntity
     * @return void
     */
    public function testNewFailsIfNoMapperFound($mapperlessEntity)
    {
        $this->mapperRegistry->shouldReceive('hasMapperForEntity')
            ->with($mapperlessEntity)
            ->once()
            ->andReturn(false);

        $this->setExpectedException('EntityWire\UnitOfWork\Exception\EntityMapperNotFoundException');

        $this->unitOfWork->registerNew($mapperlessEntity);
    }

    /**
     * @dataProvider mappedEntityProvider
     *
     * @param array $mappedEntities
     * @return void
     */
    public function testNewChecksPresenceOfMapper(array $mappedEntities)
    {
        foreach ($mappedEntities as $mappedEntity) {
            $entity = array_shift($mappedEntity);

            $this->mapperRegistry->shouldReceive('hasMapperForEntity')
                ->with($entity)
                ->once()
                ->andReturn(true);

            $this->unitOfWork->registerNew($entity);
        }
    }

    /**
     * @dataProvider mappedEntityProvider
     *
     * @param array $mappedEntities
     * @return void
     */
    public function testCommitInsertsNewEntitiesIntoMapper(array $mappedEntities)
    {
        foreach ($mappedEntities as $mappedEntity) {
            list($entity, $mapper) = $mappedEntity;

            $this->mapperRegistry->shouldReceive('hasMapperForEntity')
                ->with($entity)
                ->once()
                ->andReturn(true);

            $this->mapperRegistry->shouldReceive('getMapperForEntity')
                ->with($entity)
                ->once()
                ->andReturn($mapper);

            $mapper->shouldReceive('insert')
                ->with($entity)
                ->once();

            $this->unitOfWork->registerNew($entity);
        }

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
