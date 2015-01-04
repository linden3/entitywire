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
    public function hasMapFor($entity);
    /**
     * @param mixed $entity
     * @return bool
     */
    public function insert($entity);
    /**
     * @param mixed $entity
     * @return bool
     */
    public function delete($entity);
}
