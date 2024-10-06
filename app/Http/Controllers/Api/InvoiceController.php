<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends BaseController
{
    public function index()
    {
        return sendResponse(InvoiceResource::collection(Invoice::paginate()));
    }


    public function store(Request $request)
    {
        $data = customValidation($request, [
            'name' => 'required|string|max:255',
            'customer_id' => 'required|integer|exists:customers,id',
            'products' => 'required|array',
            'products.*' => 'integer|exists:products,id'
        ]);

        $invoice = Invoice::create($data);

        $this->saveProductAndTax($request, $data, $invoice);
        
        return sendResponse(new InvoiceResource($invoice), 'Invoice created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        return sendResponse(new InvoiceResource($invoice));
    }

    public function edit(Invoice $invoice)
    {
        return sendResponse(new InvoiceResource($invoice));
    }

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

    public function saveProductAndTax($request, $data, $invoice) {
        if ($request->has('products')) {
            $invoice->products()->sync($data['products']);
        }

        determineTax($invoice);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return sendResponse([], 'Invoice deleted successfully');
    }
}
