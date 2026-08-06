<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Models\StockItemImage;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockItemImageController extends Controller
{
    /**
     * Display the main workspace page.
     */
    public function index()
    {
        return view('inventory.stock_item_images.index');
    }

    /**
     * AJAX search by item code.
     */
    public function search(Request $request)
    {
        $request->validate([
            'item_code' => 'required|string',
        ]);

        $itemCode = trim($request->item_code);

        $stockItem = StockItem::with([
            'product.category',
            'images',
            'branch',
            'counter',
            'subProduct',
            'size'
        ])
        ->where('item_code', $itemCode)
        ->first();

        if (!$stockItem) {
            return response()->json([
                'success' => false,
                'message' => 'Stock item not found. Please verify the Item Code.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'html' => view('inventory.stock_item_images._workspace', compact('stockItem'))->render(),
        ]);
    }

    /**
     * AJAX upload image for a stock item.
     */
    public function upload(Request $request, StockItem $stockItem)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        try {
            $path = ImageUploadService::upload($request->file('image'), 'stockitemimg');

            $isDefault = !$stockItem->images()->exists();

            $stockItem->images()->create([
                'image_path' => $path,
                'is_default' => $isDefault,
            ]);

            $stockItem->load('images');

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully.',
                'html' => view('inventory.stock_item_images._workspace', compact('stockItem'))->render(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AJAX set default image for a stock item.
     */
    public function setDefault(StockItem $stockItem, StockItemImage $image)
    {
        if ($image->stock_item_id !== $stockItem->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        DB::transaction(function () use ($stockItem, $image) {
            $stockItem->images()->update(['is_default' => false]);
            $image->update(['is_default' => true]);
        });

        $stockItem->load('images');

        return response()->json([
            'success' => true,
            'message' => 'Default image updated successfully.',
            'html' => view('inventory.stock_item_images._workspace', compact('stockItem'))->render(),
        ]);
    }

    /**
     * AJAX delete an image of a stock item.
     */
    public function delete(StockItem $stockItem, StockItemImage $image)
    {
        if ($image->stock_item_id !== $stockItem->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        DB::transaction(function () use ($stockItem, $image) {
            ImageUploadService::delete($image->image_path);
            $wasDefault = $image->is_default;
            $image->delete();

            if ($wasDefault) {
                $nextImage = $stockItem->images()->first();
                if ($nextImage) {
                    $nextImage->update(['is_default' => true]);
                }
            }
        });

        $stockItem->load('images');

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.',
            'html' => view('inventory.stock_item_images._workspace', compact('stockItem'))->render(),
        ]);
    }
}
