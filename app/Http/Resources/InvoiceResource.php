<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'products' => ProductResource::collection($this->products),
            'total_quantity' => $this->products->sum('quantity'),
            'total_price' => $this->products->sum('quantity * unit_price'),
            'tax' => $this->tax,
            'total_amount_with_tax' => $this->total_amount + $this->tax
        ];
    }
}
