<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'designation' => $this->designation,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'invoices' => InvoiceResource::collection($this->invoices),
            'total_sales' => $this->invoices->sum('quantity'),
            'total_orders' => $this->invoices->count(),
            'total_revenue' => $this->invoices->sum('quantity') * $this->unit_price,
        ];
    }
}
