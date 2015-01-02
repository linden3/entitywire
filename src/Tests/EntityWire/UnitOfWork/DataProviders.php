<?php
namespace Tests\EntityWire\UnitOfWork;

use Mockery;

trait DataProviders
{
    /**
     * @return array
     */
    public function nonObjectProvider()
    {
        return array(
            array("NULL", null),
            array("string", "string"),
            array("integer", 1),
            array("array", array()),
        );
    }

    /**
     * @return array
     */
    public function mappedEntityProvider()
    {
        return array(array(array(
            array(Mockery::mock('MappedEntity1'), Mockery::mock('Mapper1')),
            array(Mockery::mock('MappedEntity2'), Mockery::mock('Mapper2')),
            array(Mockery::mock('MappedEntity3'), Mockery::mock('Mapper3'))
        )));
    }

    /**
     * @return array
     */
    public function mapperlessEntityProvider()
    {
        return array(array(
            Mockery::mock('MapperlessEntity')
        ));
    }
}
