<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\ActivityLogService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    /**
     * Display a listing of settings.
     */
    public function index(Request $request)
    {
        $query = Setting::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('value', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $settings = $query->latest()->paginate(15)->withQueryString();

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('settings._table', compact('settings'))->render()
            ]);
        }

        return view('settings.index', compact('settings'));
    }

    /**
     * Show the form for creating a new setting.
     */
    public function create()
    {
        return view('settings.create');
    }

    /**
     * Store a newly created setting in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key'         => ['required', 'string', 'max:255', 'unique:settings,key'],
            'value'       => ['nullable', 'string'],
            'group'       => ['nullable', 'string', 'max:100'],
            'type'        => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:555'],
        ]);

        DB::transaction(function () use ($validated) {
            $setting = SettingService::set(
                key: $validated['key'],
                value: $validated['value'] ?? '',
                group: $validated['group'] ?? 'general',
                type: $validated['type'] ?? 'string',
                description: $validated['description'] ?? null
            );

            ActivityLogService::log(
                'Setting',
                'CREATE',
                $setting->id,
                "Created Setting: {$setting->key}"
            );
        });

        return redirect()
            ->route('settings.index')
            ->with('success', 'Setting created successfully.');
    }

    /**
     * Show the form for editing the specified setting.
     */
    public function edit(Setting $setting)
    {
        return view('settings.edit', compact('setting'));
    }

    /**
     * Update the specified setting in storage.
     */
    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'key'         => ['required', 'string', 'max:255', Rule::unique('settings', 'key')->ignore($setting->id)],
            'value'       => ['nullable', 'string'],
            'group'       => ['nullable', 'string', 'max:100'],
            'type'        => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:555'],
        ]);

        DB::transaction(function () use ($setting, $validated) {
            $oldKey = $setting->key;

            $setting->update([
                'key'         => $validated['key'],
                'value'       => $validated['value'] ?? '',
                'group'       => $validated['group'] ?? 'general',
                'type'        => $validated['type'] ?? 'string',
                'description' => $validated['description'] ?? null,
            ]);

            SettingService::clearCache($oldKey);
            SettingService::clearCache($validated['key']);

            ActivityLogService::log(
                'Setting',
                'UPDATE',
                $setting->id,
                "Updated Setting: {$setting->key}"
            );
        });

        return redirect()
            ->route('settings.index')
            ->with('success', 'Setting updated successfully.');
    }

    /**
     * Remove the specified setting from storage.
     */
    public function destroy(Setting $setting)
    {
        DB::transaction(function () use ($setting) {
            $key = $setting->key;
            $setting->delete();

            SettingService::clearCache($key);

            ActivityLogService::log(
                'Setting',
                'DELETE',
                0,
                "Deleted Setting: {$key}"
            );
        });

        return redirect()
            ->route('settings.index')
            ->with('success', 'Setting deleted successfully.');
    }
}
