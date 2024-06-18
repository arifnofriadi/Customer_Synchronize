<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Repositories\CustomerRepository;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    private $customerRepository;

     public function __construct()
     {
        $this->customerRepository = new CustomerRepository();
     }

    public function index()
    {
        try {
            $data = $this->customerRepository->getCustomers();

            return $this->success($data, 'get all customers successfully');
        } catch (\Throwable $th) {
            Log::error([
                'Message'   => $th->getMessage(),
                'On Line'   => $th->getLine(),
                'On File'   => $th->getFile(),
            ]);

            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'          =>  'required',
            'email'         =>  'required|email:rfc,dns|unique:customers,email',
            'phone_number'  =>  'required|numeric|unique:customers,phone_number',
            "birth_date"    =>  'required|date',
            'address'       =>  'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validation->errors()
            ], 422);
        }

        try {
            $validatedData = $validation->validated();
            $response = $this->customerRepository->createCustomer($validatedData);

            return $this->success($response, 'customer created successfully');
        } catch (\Throwable $th) {
            Log::error([
                'Message'   => $th->getMessage(),
                'On Line'   => $th->getLine(),
                'On File'   => $th->getFile(),
            ]);

            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        try {
            $data = $this->customerRepository->getCustomerById($request->route('id'));

            return $this->success($data, 'get details of customer successfully');
        } catch (\Throwable $th) {
            Log::error([
                'Message'   => $th->getMessage(),
                'On Line'   => $th->getLine(),
                'On File'   => $th->getFile(),
            ]);

            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function synchronize(Request $request)
    {
        try {
           $response = $this->customerRepository->synchronizeCustomer($request->all());

           return $this->success($response, 'synchronize customer successfully');
        } catch (\Throwable $th) {
            Log::error([
                'Message'   => $th->getMessage(),
                'On Line'   => $th->getLine(),
                'On File'   => $th->getFile(),
            ]);

            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $customerId)
    {
        $customer = $this->customerRepository->findCustomerById($customerId);
        if (!$customer) return $this->error('customer not found', 404);

        $validation = Validator::make($request->all(), [
            'name'          =>  'required',
            'email'         => [
                'required',
                'email:rfc,dns',
                Rule::unique('customers', 'email')->ignore($customerId),
            ],
            'phone_number'  => [
                'required',
                'numeric',
                Rule::unique('customers', 'phone_number')->ignore($customerId),
            ],

            "birth_date"    =>  'required|date',
            'address'       =>  'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validation->errors()
            ], 422);
        }

        try {
            $validatedData = $validation->validated();
            $response = $this->customerRepository->updateCustomer($customerId, $validatedData);

            return $this->success($response, 'customer updated successfully');
        } catch (\Throwable $th) {
            Log::error([
                'Message'   => $th->getMessage(),
                'On Line'   => $th->getLine(),
                'On File'   => $th->getFile(),
            ]);

            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($customerId)
    {
        try {
            $customer = $this->customerRepository->findCustomerById($customerId);
            if (!$customer) return $this->error('customer not found', 404);
            $response = $this->customerRepository->deleteCustomer($customerId);
            return $this->success($response, 'customer deleted successfully');
        } catch (\Throwable $th) {
            Log::error([
                'Message'   => $th->getMessage(),
                'On Line'   => $th->getLine(),
                'On File'   => $th->getFile(),
            ]);

            return $this->error($th->getMessage(), 400);
        }
    }

    public function search($keyword)
    {
        try {
            $response = $this->customerRepository->searchCustomer(['keyword' => $keyword]);
            return $this->success($response, 'search customer successfully');
        } catch (\Throwable $th) {
            Log::error([
                'Message'   => $th->getMessage(),
                'On Line'   => $th->getLine(),
                'On File'   => $th->getFile(),
            ]);

            return $this->error($th->getMessage(), 400);
        }
    }

}
