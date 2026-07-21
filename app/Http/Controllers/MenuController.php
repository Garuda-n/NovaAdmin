<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::with('children')
            ->roots()
            ->orderBy('order')
            ->paginate(5)
            ->withQueryString();

        return view('menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parents = Menu::roots()->orderBy('order')->get();
        return view('menus.create', compact('parents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:menus,id',
            'permission_slug' => 'nullable|string|max:100',
            'order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {

            $menu = Menu::create($validated);

            ActivityLogService::log(
                'Menu',
                'CREATED',
                $menu->id,
                "Created Menu '{$menu->name}'"
            );

            DB::commit();

            return redirect()
                ->route('menus.index')
                ->with('success', 'Menu created successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the menu.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        $parents = Menu::roots()
            ->where('id', '!=', $menu->id)
            ->orderBy('order')
            ->get();

        return view('menus.edit', compact('menu', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'parent_id' => [
                'nullable',
                'exists:menus,id',
                Rule::notIn([$menu->id]),
            ],
            'permission_slug' => 'nullable|string|max:100',
            'order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {

            $menu->update($validated);

            ActivityLogService::log(
                'Menu',
                'UPDATED',
                $menu->id,
                "Updated Menu '{$menu->name}'"
            );

            DB::commit();

            return redirect()
                ->route('menus.index')
                ->with('success', 'Menu updated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the menu.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        DB::beginTransaction();

        try {

            $menuName = $menu->name;
            $menu->delete();

            ActivityLogService::log(
                'Menu',
                'DELETED',
                $menu->id,
                "Deleted Menu '{$menuName}'"
            );

            DB::commit();

            return redirect()
                ->route('menus.index')
                ->with('success', 'Menu deleted successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with('error', 'Something went wrong while deleting the menu.');
        }
    }
}
