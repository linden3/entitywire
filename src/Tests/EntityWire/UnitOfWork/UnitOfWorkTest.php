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
     * @var \Mockery\MockInterface | \EntityWire\Mapper\MapperInterface
     */
    private $mapper;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->mapper = \Mockery::mock('\EntityWire\Mapper\MapperInterface');

        $this->unitOfWork = new UnitOfWork($this->mapper);
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
        $this->mapper->shouldReceive('mapsEntity')
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
            $this->mapper->shouldReceive('mapsEntity')
                ->with($mappedEntity)
                ->once()
                ->andReturn(true);

            $this->unitOfWork->registerNew($mappedEntity);
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
        $this->mapper->shouldReceive('mapsEntity')
            ->andReturn(true);

        foreach ($mappedEntities as $mappedEntity) {

            $this->mapper->shouldReceive('insert')
                ->with($mappedEntity)
                ->once();

            $this->unitOfWork->registerNew($mappedEntity);
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
