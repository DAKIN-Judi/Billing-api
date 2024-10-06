<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class InvoiceController extends BaseController
{

    /**
     * @OA\Get(
     *     path="/api/invoices",
     *     tags={"Invoices"},
     *     summary="Get list of invoices",
     *     description="Returns paginated list of invoices",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Invoice name"),
     *                 @OA\Property(property="customer_id", type="integer", example=10),
     *                 @OA\Property(property="products", type="array", @OA\Items(type="integer", example=5)),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-06 12:30:00"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-06 14:30:00")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return sendResponse(InvoiceResource::collection(Invoice::paginate()));
    }


    /**
     * @OA\Post(
     *     path="/api/invoices",
     *     tags={"Invoices"},
     *     summary="Create a new invoice",
     *     description="Create a new invoice with specified details",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Invoice 001"),
     *             @OA\Property(property="customer_id", type="integer", example=1),
     *             @OA\Property(property="products", type="array", @OA\Items(type="integer", example=1))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invoice created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Invoice 001"),
     *             @OA\Property(property="customer_id", type="integer", example=1),
     *             @OA\Property(property="products", type="array", @OA\Items(type="integer", example=1)),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-06 12:30:00"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-06 12:30:00")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $data = customValidation($request, [
            'name' => 'required|string|max:255',
            'customer_id' => 'required|integer|exists:customers,id',
            'products' => 'required|array',
            'products.*.id' => 'integer|exists:products,id',
            'products.*.quantity' => 'integer',
            'products.*.unit_price' => 'integer'
        ]);

        $invoice = Invoice::create($data);

        $this->saveProductAndTax($request, $data, $invoice);

        return sendResponse(new InvoiceResource($invoice), 'Invoice created successfully');
    }


    /**
     * @OA\Get(
     *     path="/api/invoices/{id}",
     *     tags={"Invoices"},
     *     summary="Get invoice details",
     *     description="Get details of a specific invoice by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Invoice ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Invoice name"),
     *             @OA\Property(property="customer_id", type="integer", example=10),
     *             @OA\Property(property="products", type="array", @OA\Items(type="integer", example=5)),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-06 12:30:00"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-06 14:30:00")
     *         )
     *     )
     * )
     */

    public function show(Invoice $invoice)
    {
        return sendResponse(new InvoiceResource($invoice));
    }


    /**
     * @OA\Put(
     *     path="/api/invoices/{id}",
     *     tags={"Invoices"},
     *     summary="Update invoice",
     *     description="Update details of a specific invoice",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Invoice ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Invoice"),
     *             @OA\Property(property="products", type="array", @OA\Items(type="integer", example=1))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invoice updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Updated Invoice"),
     *             @OA\Property(property="customer_id", type="integer", example=1),
     *             @OA\Property(property="products", type="array", @OA\Items(type="integer", example=1)),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-06 12:30:00"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-06 12:30:00")
     *         )
     *     )
     * )
     */
    public function update(Request $request, Invoice $invoice)
    {
        $data = customValidation($request, [
            'name' => 'sometimes|string|max:255',
            'products' => 'sometimes|array',
            'products.*' => 'integer|exists:products,id'
        ]);

        $invoice->update($data);

        $this->saveProductAndTax($request, $data, $invoice);

        return sendResponse(new InvoiceResource($invoice), 'Invoice updated successfully');
    }

    public function saveProductAndTax($request, $data, $invoice)
    {
        // if ($request->has('products')) {
        //     foreach ($data['products'] as $product) {
        //         $productsData[$product['id']] = [
        //             'quantity' => $product['quantity'],
        //             'unit_price' => $product['unit_price']
        //         ];
        //         $invoice->products()->syncWithoutDetaching($productsData);

        //     }

        // }

        determineTax($invoice);
    }

    /**
     * @OA\Delete(
     *     path="/api/invoices/{id}",
     *     tags={"Invoices"},
     *     summary="Delete invoice",
     *     description="Delete a specific invoice by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Invoice ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invoice deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invoice deleted successfully")
     *         )
     *     )
     * )
     */


    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return sendResponse([], 'Invoice deleted successfully');
    }
}
