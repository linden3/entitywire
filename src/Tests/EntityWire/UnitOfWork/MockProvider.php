<?php
namespace Tests\EntityWire\UnitOfWork;

trait MockProvider
{
    private function mockTransactionManager()
    {
        return \Mockery::mock('\EntityWire\Transaction\TransactionManagerInterface', function($transactionManager) {
            $transactionManager->shouldReceive('startTransaction')->byDefault();
            $transactionManager->shouldReceive('commitTransaction')->byDefault();
        });
    }

    private function mockEntityMapper()
    {
        return \Mockery::mock('\EntityWire\Mapper\EntityMapperInterface', function($entityMapper) {
            $entityMapper->shouldReceive('hasMapFor')->andReturn(true)->byDefault();
            $entityMapper->shouldReceive('insert')->byDefault();
            $entityMapper->shouldReceive('delete')->byDefault();
        });
    }
}
