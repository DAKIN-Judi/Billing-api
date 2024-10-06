<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['designation', 'description', 'quantity', 'unit_price'];


    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_products')
                    ->withPivot('quantity', 'unit_price');
    }
}
