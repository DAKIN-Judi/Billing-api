<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'billingAddress' => $this->billingAddress,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // 'invoices' => InvoiceResource::collection($this->invoices),
            // 'total_orders' => $this->invoices->count(),
            // 'total_amount' => $this->invoices->sum('amount'),
            // 'average_order_amount' => $this->invoices->avg('amount'),
            // 'last_order_date' => $this->invoices->max('created_at'),
            // 'last_order_amount' => $this->invoices->max('amount'),
        ];
    }
}
