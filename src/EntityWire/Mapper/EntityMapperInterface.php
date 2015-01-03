<?php
namespace EntityWire\Mapper;

/**
 * Interface RegistryInterface
 * @package EntityWire\Mapper
 */
interface EntityMapperInterface
{
    /**
     * @param mixed $entity
     * @return bool
     */
    public function mapsEntity($entity);
}
