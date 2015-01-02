<?php
namespace EntityWire\Mapper;

/**
 * Interface RegistryInterface
 * @package EntityWire\Mapper
 */
interface RegistryInterface
{
    /**
     * @param mixed $entity
     * @return bool
     */
    public function hasMapperForEntity($entity);
}
