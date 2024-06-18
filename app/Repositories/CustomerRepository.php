<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Interfaces\CustomerRepositoryInterface;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function getCustomers()
    {
        $customers = Customer::query()
        ->select('id', 'name', 'email', 'phone_number', 'address', 'created_at', 'updated_at')
        ->latest()
        ->paginate(10);

        return $customers;
    }

    public function getCustomerById($customerId)
    {
        $customer = Customer::find($customerId);
        if (!$customer) throw new \Exception('Customer not found');

        return Customer::findOrFail($customerId);
    }

    public function createCustomer(array $data)
    {
        $data = Customer::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'phone_number'  => $data['phone_number'],
            'birth_date'    => $data['birth_date'],
            'address'       => $data['address'],
        ]);

        return $data;
    }

    public function findCustomerById($customerId)
    {
        return Customer::find($customerId);
    }


    public function updateCustomer($customerId, array $data)
    {
        $customer = $this->findCustomerById($customerId);

        return $customer->update($data);
    }

    public function deleteCustomer($customerId)
    {
        return Customer::destroy($customerId);
    }

    public function synchronizeCustomer(array $data)
    {
        $customer = Customer::where('phone_number', $data['phone_number'])->first();

        if ($customer == null) {
            $customerEmail = Customer::where('email', $data['email'])->first();
            if ($customerEmail) throw new \Exception('email already exists');

            $customer = $this->createCustomer($data);
            return [
                'customer'          => $customer,
                'syncronize type'   => 'new customer created',
            ];
        } else {

            $customer->name         = $data['name'];
            $customer->email        = $data['email'];
            $customer->phone_number = $data['phone_number'];
            $customer->birth_date   = $data['birth_date'];
            $customer->address      = $data['address'];
            $customer->update();

            return [
                'customer'          => $customer,
                'syncronize type'   => 'data updated',
            ];
        }
    }

    public function searchCustomer(array $data)
    {
        $keyword = $data['keyword'];

        $customers = Customer::where('name', 'like', '%'.$keyword.'%')
                             ->orWhere('phone_number', 'like', '%'.$keyword.'%')
                             ->get();

        if ($customers->isEmpty()) throw new \Exception("Data with the keyword '{$keyword}' not found", 404);

        return $customers;
    }
}

?>
