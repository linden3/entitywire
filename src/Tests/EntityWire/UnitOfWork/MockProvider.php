<?php
namespace Tests\EntityWire\UnitOfWork;

trait MockProvider
{
    private function mockTransactionManager()
    {
        $transactionManager = \Mockery::mock('\EntityWire\Transaction\TransactionManagerInterface');

        $transactionManager->shouldReceive('startTransaction')->byDefault();
        $transactionManager->shouldReceive('commitTransaction')->byDefault();

        return $transactionManager;
    }

    private function mockEntityMapper()
    {
        $entityMapper = \Mockery::mock('\EntityWire\Mapper\EntityMapperInterface');

        $entityMapper->shouldReceive('hasMapFor')->andReturn(true)->byDefault();
        $entityMapper->shouldReceive('insert')->byDefault();

        return $entityMapper;
    }
}
