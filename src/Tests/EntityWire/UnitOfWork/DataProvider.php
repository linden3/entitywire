<?php
namespace Tests\EntityWire\UnitOfWork;

use Mockery;

trait DataProvider
{
    /**
     * @return array
     */
    public function nonObject()
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
        return $this->wrapSingle(
            array(
                Mockery::mock('Entity1'),
                Mockery::mock('Entity2'),
                Mockery::mock('Entity3')
            )
        );
    }

    /**
     * @return array
     */
    public function singleEntity()
    {
        return $this->wrapSingle(
            Mockery::mock('Entity')
        );
    }

    /**
     * @param mixed $data
     * @return array
     */
    private function wrapSingle($data)
    {
        return array(array(
            $data
        ));
    }
}
