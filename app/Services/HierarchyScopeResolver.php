<?php

namespace App\Services;

use App\Models\User;
use App\Models\School;

class HierarchyScopeResolver
{
    /**
     * Resolve array of school IDs accessible by the given user based on their territorial role and scope.
     * Returns null if super_admin / global view (no filter).
     *
     * @param User $user
     * @return array<int>|null
     */
    public function resolveVisibleSchoolIds(User $user): ?array
    {
        // 1. Super Admin has global access (null = no filter)
        if ($user->hasRole('super_admin') || $user->hasRole('Super Admin')) {
            return null;
        }

        // 2. Province Inspector / Admin -> All schools in user's province
        if ($user->hasRole('province_admin') || $user->hasRole('province_inspector')) {
            if ($user->province_id) {
                return School::where('province_id', $user->province_id)->pluck('id')->toArray();
            }
        }

        // 3. District Inspector / Admin -> All schools in user's district
        if ($user->hasRole('district_admin') || $user->hasRole('district_inspector')) {
            if ($user->district_id) {
                return School::where('district_id', $user->district_id)->pluck('id')->toArray();
            }
        }

        // 4. ZIP Coordinator / Admin -> All schools in user's ZIP
        if ($user->hasRole('zip_coordinator') || $user->hasRole('zip_admin')) {
            if ($user->zip_id) {
                return School::where('zip_id', $user->zip_id)->pluck('id')->toArray();
            }
        }

        // Fallback checks by territorial column directly if user has province_id, district_id or zip_id
        if ($user->province_id) {
            return School::where('province_id', $user->province_id)->pluck('id')->toArray();
        }

        if ($user->district_id) {
            return School::where('district_id', $user->district_id)->pluck('id')->toArray();
        }

        if ($user->zip_id) {
            return School::where('zip_id', $user->zip_id)->pluck('id')->toArray();
        }

        // 5. School level user -> Return user's school_id
        if ($user->school_id) {
            return [(int) $user->school_id];
        }

        return [];
    }

    /**
     * Resolve territorial scope details for a user.
     *
     * @param User $user
     * @return array{level: string, id: int|null}
     */
    public function resolveTerritorialScope(User $user): array
    {
        if ($user->hasRole('super_admin') || $user->hasRole('Super Admin')) {
            return ['level' => 'super', 'id' => null];
        }
        if ($user->hasRole('province_admin') || $user->hasRole('province_inspector') || $user->province_id) {
            return ['level' => 'province', 'id' => $user->province_id];
        }
        if ($user->hasRole('district_admin') || $user->hasRole('district_inspector') || $user->district_id) {
            return ['level' => 'district', 'id' => $user->district_id];
        }
        if ($user->hasRole('zip_coordinator') || $user->hasRole('zip_admin') || $user->zip_id) {
            return ['level' => 'zip', 'id' => $user->zip_id];
        }
        return ['level' => 'school', 'id' => $user->school_id];
    }
}
