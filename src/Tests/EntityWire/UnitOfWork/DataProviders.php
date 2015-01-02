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
    public function newEntityProvider()
    {
        return array(array(array(
            Mockery::mock('MappedEntity'),
            Mockery::mock('MappedEntity'),
            Mockery::mock('MappedEntity')
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
