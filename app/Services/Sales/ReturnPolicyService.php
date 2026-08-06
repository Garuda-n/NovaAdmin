<?php

namespace App\Services\Sales;

use App\Models\Product;
use Illuminate\Validation\ValidationException;

class ReturnPolicyService
{
    /**
     * Validate the category return policies for a product.
     *
     * @param Product $product
     * @param string $returnDate
     * @param string $invoiceDate
     * @return void
     * @throws ValidationException
     */
    public function validateCategoryPolicy(Product $product, string $returnDate, string $invoiceDate): void
    {
        $product->loadMissing('category');
        $category = $product->category;

        if (!$category) {
            return; // No category mapped, skip policy checks
        }

        // 1. Returnability Check
        if (!$category->is_returnable) {
            throw ValidationException::withMessages([
                'sales_return' => ["Products belonging to the category '{$category->name}' are configured as non-returnable."],
            ]);
        }

        // 2. Timeline Window Check
        $invoiceTime = strtotime($invoiceDate);
        $returnTime = strtotime($returnDate);

        if ($returnTime < $invoiceTime) {
            throw ValidationException::withMessages([
                'return_date' => ['The return date cannot be prior to the original invoice date.'],
            ]);
        }

        $ageDays = (int) round(($returnTime - $invoiceTime) / 86400);
        $window = (int) ($category->return_window_days ?? 30);

        if ($ageDays > $window) {
            throw ValidationException::withMessages([
                'sales_return' => ["The return period of {$window} days has expired for category '{$category->name}' items. Invoiced: {$invoiceDate}, Returning: {$returnDate} (Days elapsed: {$ageDays})."],
            ]);
        }
    }
}
