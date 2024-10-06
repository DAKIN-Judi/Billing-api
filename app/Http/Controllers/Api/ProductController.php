<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends BaseController
{

    /**
     * @OA\Get(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Get all products",
     *     description="Returns a paginated list of products",
     *     security={ {"bearer": {}} },
     *     @OA\Response(
     *         response=200,
     *         description="List of products",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref=""))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */

    public function index()
    {
        return sendResponse(ProductResource::collection(Product::paginate()));
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Create a new product",
     *     description="Creates a new product with the provided details",
     *     security={ {"bearer": {}} },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="designation", type="string", example="Product Name"),
     *             @OA\Property(property="description", type="string", example="Product Description"),
     *             @OA\Property(property="unit_price", type="integer", example=100)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref=""),
     *             @OA\Property(property="message", type="string", example="Product created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */

    public function store(Request $request)
    {
        $data = customValidation($request, [
            'designation' => 'required|string|max:255',
            'description' => 'required|string',
            'quantity' => 'required|integer',
            'unit_price' => 'required|int'
        ]);

        $customer = Product::create($data);
        return sendResponse(new ProductResource($customer), 'Product created successfully');
    }


    /**
     * @OA\Get(
     *     path="/api/products/{product}",
     *     tags={"Products"},
     *     summary="Get a specific product by ID",
     *     description="Returns the details of a product by its ID",
     *     security={ {"bearer": {}} },
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product details",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */

    public function show(Product $product)
    {
        return sendResponse(new ProductResource($product));
    }


    /**
     * @OA\Put(
     *     path="/api/products/{product}",
     *     tags={"Products"},
     *     summary="Update a specific product",
     *     description="Updates a product's details by its ID",
     *     security={ {"bearer": {}} },
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="designation", type="string", example="Updated Product Name"),
     *             @OA\Property(property="description", type="string", example="Updated Product Description"),
     *             @OA\Property(property="quantity", type="integer", example=1),
     *             @OA\Property(property="unit_price", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref=""),
     *             @OA\Property(property="message", type="string", example="Product updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */

    public function update(Request $request, Product $product)
    {
        $data = customValidation($request, [
            'designation' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'quantity' => 'sometimes|integer',
            'unit_price' => 'sometimes|int'
        ]);

        $product->update($data);
        return sendResponse(new ProductResource($product), 'Product updated successfully');
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{product}",
     *     tags={"Products"},
     *     summary="Delete a product",
     *     description="Deletes a product by its ID",
     *     security={ {"bearer": {}} },
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */

    public function destroy(Product $product)
    {
        $product->delete();
        return sendResponse([], 'Product deleted successfully');
    }
}
