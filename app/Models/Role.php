<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['name'];

    /**
     * In-memory cache for permission slugs during request lifecycle.
     */
    protected ?array $cachedPermissionSlugs = null;

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Check if role has a specific permission slug (optimized with in-memory caching).
     *
     * @param string $slug
     * @return bool
     */
    public function hasPermission(string $slug): bool
    {
        if ($this->cachedPermissionSlugs === null) {
            $this->cachedPermissionSlugs = $this->permissions()->pluck('slug')->toArray();
        }

        return in_array($slug, $this->cachedPermissionSlugs, true);
    }
}
