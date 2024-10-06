<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/customers",
     *     tags={"Customers"},
     *     summary="Get all customers",
     *     description="Returns a list of customers with pagination",
     *     security={ {"bearer": {}} },
     *     @OA\Response(
     *         response=200,
     *         description="List of customers",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="")
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string"),
     *                 @OA\Property(property="last", type="string"),
     *                 @OA\Property(property="prev", type="string"),
     *                 @OA\Property(property="next", type="string"),
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated."
     *             )
     *         ),
     *     )
     * )
     */

    public function index()
    {
        return sendResponse(CustomerResource::collection(Customer::paginate()));
    }


    /**
     * @OA\Post(
     *     path="/api/customers",
     *     tags={"Customers"},
     *     summary="Create a new customer",
     *     description="This endpoint allows you to create a new customer by providing their name, email, and billing address.",
     *     operationId="createCustomer",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "billingAddress"},
     *             @OA\Property(property="name", type="string", example="John Doe", description="Customer's full name"),
     *             @OA\Property(property="email", type="string", example="johndoe@example.com", description="Customer's email address"),
     *             @OA\Property(property="billingAddress", type="string", example="123 Main St, City, Country", description="Customer's billing address")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Customer created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref=""
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Customer created successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The given data was invalid."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties={
     *                     "type": "array",
     *                     "items": {
     *                         "type": "string",
     *                         "example": "The name field is required."
     *                     }
     *                 }
     *             )
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function store(Request $request)
    {
        $data = customValidation($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'billingAddress' => 'required|string'
        ]);

        $customer = Customer::create($data);
        return sendResponse(new CustomerResource($customer), 'Customer created successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/customers/{customer}",
     *     tags={"Customers"},
     *     summary="Get a specific customer by ID",
     *     description="Returns the details of a customer by ID",
     *     security={ {"bearer": {}} },
     *     @OA\Parameter(
     *         name="customer",
     *         in="path",
     *         required=true,
     *         description="Customer ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer details",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Customer not found."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated."
     *             )
     *         )
     *     )
     * )
     */

    public function show(Customer $customer)
    {
        return sendResponse(new CustomerResource($customer));
    }


    /**
     * @OA\Put(
     *     path="/api/customers/{customer}",
     *     tags={"Customers"},
     *     summary="Update a specific customer",
     *     description="Updates a customer's information by ID",
     *     security={ {"bearer": {}} },
     *     @OA\Parameter(
     *         name="customer",
     *         in="path",
     *         required=true,
     *         description="Customer ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="billingAddress", type="string", example="1234 Main St")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref=""),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Customer updated successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Customer not found."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated."
     *             )
     *         )
     *     )
     * )
     */

    public function update(Request $request, Customer $customer)
    {
        $data = customValidation($request, [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'billingAddress' => 'sometimes|string'
        ]);

        $customer->update($data);
        return sendResponse(new CustomerResource($customer), 'Customer updated successfully');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return sendResponse([], 'Customer deleted successfully');
    }
}
