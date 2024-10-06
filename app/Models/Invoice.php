<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'name', 'invoice_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            $invoice->invoice_id = self::generateRandomInvoiceId();
        });
    }

    private static function generateRandomInvoiceId()
    {
        return 'INV-' . strtoupper(\Illuminate\Support\Str::random(8));
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'invoice_products')
                    ->withPivot('quantity', 'unit_price');
    }

    public function invoiceProducts() {
        return $this->hasMany(InvoiceProduct::class);
    }
}
