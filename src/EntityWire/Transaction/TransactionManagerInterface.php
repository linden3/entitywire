<?php
namespace EntityWire\Transaction;

interface TransactionManagerInterface
{
    public function start();
    public function commit();
    public function rollback();
}
