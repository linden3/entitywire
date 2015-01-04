<?php
namespace Tests\EntityWire\UnitOfWork;

use Mockery;

trait DataProvider
{
    /**
     * @return array
     */
    public function nonObjects()
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
    public function multipleEntities()
    {
        return array(array(
                Mockery::mock('Entity1'),
                Mockery::mock('Entity2'),
                Mockery::mock('Entity3')
        ));
    }

    /**
     * @return array
     */
    public function singleEntity()
    {
        return array(array(
            Mockery::mock('Entity')
        ));
    }
}
