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
        return $this->wrapSingle(
            array(
                Mockery::mock('MappedEntity1'),
                Mockery::mock('MappedEntity2'),
                Mockery::mock('MappedEntity3')
            )
        );
    }

    /**
     * @return array
     */
    public function mapperlessEntityProvider()
    {
        return $this->wrapSingle(
            Mockery::mock('MapperlessEntity')
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
