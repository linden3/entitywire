<?php
namespace Tests\EntityWire\UnitOfWork;

trait MockProvider
{
    private function mockTransactionManager()
    {
        $transactionManager = \Mockery::mock('\EntityWire\Transaction\TransactionManagerInterface');

        $transactionManager->shouldReceive('start')->byDefault();
        $transactionManager->shouldReceive('commit')->byDefault();

        return $transactionManager;
    }

    private function mockEntityMapper()
    {
        $entityMapper = \Mockery::mock('\EntityWire\Mapper\EntityMapperInterface');

        $entityMapper->shouldReceive('mapsEntity')->andReturn(true)->byDefault();
        $entityMapper->shouldReceive('insert')->byDefault();

        return $entityMapper;
    }
}
