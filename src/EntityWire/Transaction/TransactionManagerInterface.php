<?php
namespace EntityWire\Transaction;

interface TransactionManagerInterface
{
    public function startTransaction();
    public function commitTransaction();
    public function rollbackTransaction();
}
