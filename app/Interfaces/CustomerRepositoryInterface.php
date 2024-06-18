<?php

namespace App\Interfaces;

interface CustomerRepositoryInterface
{
    public function getCustomers();
    public function getCustomerById($customerId);
    public function createCustomer(array $data);
    public function updateCustomer($customerId, array $data);
    public function deleteCustomer($customerId);
    public function synchronizeCustomer(array $data);
    public function searchCustomer(array $data);
}

?>
