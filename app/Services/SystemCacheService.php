<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\School;
use App\Models\Zip;
use App\Models\District;
use App\Models\Province;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\User;

class SystemCacheService
{
    /**
     * Cache TTL in seconds (1 hour default).
     */
    protected int $ttl = 3600;

    /**
     * Get cached list of all provinces.
     */
    public function getProvinces()
    {
        return Cache::remember('system_provinces_all', $this->ttl, function () {
            return Province::withCount(['districts', 'schools'])->orderBy('name')->get();
        });
    }

    /**
     * Get cached list of districts (optionally filtered by province_id).
     */
    public function getDistricts(?int $provinceId = null)
    {
        $cacheKey = $provinceId ? "system_districts_prov_{$provinceId}" : 'system_districts_all';

        return Cache::remember($cacheKey, $this->ttl, function () use ($provinceId) {
            $query = District::with(['province'])->withCount(['zips', 'schools']);
            if ($provinceId) {
                $query->where('province_id', $provinceId);
            }
            return $query->orderBy('name')->get();
        });
    }

    /**
     * Get cached list of ZIPs (optionally filtered by district_id).
     */
    public function getZips(?int $districtId = null)
    {
        $cacheKey = $districtId ? "system_zips_dist_{$districtId}" : 'system_zips_all';

        return Cache::remember($cacheKey, $this->ttl, function () use ($districtId) {
            $query = Zip::with(['district.province'])->withCount('schools');
            if ($districtId) {
                $query->where('district_id', $districtId);
            }
            return $query->orderBy('name')->get();
        });
    }

    /**
     * Get cached list of active schools with location relations.
     */
    public function getSchools(?int $zipId = null, ?int $districtId = null, ?int $provinceId = null)
    {
        $cacheKey = "system_schools_z{$zipId}_d{$districtId}_p{$provinceId}";

        return Cache::remember($cacheKey, $this->ttl, function () use ($zipId, $districtId, $provinceId) {
            $query = School::with(['province', 'district', 'zip'])->withCount('students');

            if ($zipId) {
                $query->where('zip_id', $zipId);
            } elseif ($districtId) {
                $query->where('district_id', $districtId);
            } elseif ($provinceId) {
                $query->where('province_id', $provinceId);
            }

            return $query->orderBy('name')->get();
        });
    }

    /**
     * Get cached global territorial statistics.
     */
    public function getTerritoryStats(?int $provinceId = null, ?int $districtId = null, ?int $zipId = null): array
    {
        $cacheKey = "territory_stats_p{$provinceId}_d{$districtId}_z{$zipId}";

        return Cache::remember($cacheKey, $this->ttl, function () use ($provinceId, $districtId, $zipId) {
            $schoolsQuery = School::query();
            if ($zipId) {
                $schoolsQuery->where('zip_id', $zipId);
            } elseif ($districtId) {
                $schoolsQuery->where('district_id', $districtId);
            } elseif ($provinceId) {
                $schoolsQuery->where('province_id', $provinceId);
            }

            $totalSchools = (clone $schoolsQuery)->count();
            $publicSchools = (clone $schoolsQuery)->where('type', 'Pública')->count();
            $privateSchools = (clone $schoolsQuery)->where('type', 'Privada')->count();
            $communitySchools = (clone $schoolsQuery)->whereIn('type', ['Comunitária', 'Comunitária / Privada'])->count();

            $totalZips = Zip::when($districtId, fn($q) => $q->where('district_id', $districtId))->count();
            $totalDistricts = District::when($provinceId, fn($q) => $q->where('province_id', $provinceId))->count();
            $totalProvinces = Province::count();

            $totalStudents = Student::when($provinceId || $districtId || $zipId, function ($q) use ($schoolsQuery) {
                $q->whereIn('school_id', (clone $schoolsQuery)->pluck('id'));
            })->count();

            return [
                'total_schools' => $totalSchools,
                'public_schools' => $publicSchools,
                'private_schools' => $privateSchools,
                'community_schools' => $communitySchools,
                'total_zips' => $totalZips,
                'total_districts' => $totalDistricts,
                'total_provinces' => $totalProvinces,
                'total_students' => $totalStudents,
            ];
        });
    }

    /**
     * Get cached single school details.
     */
    public function getSchoolDetails(int $schoolId)
    {
        return Cache::remember("school_details_{$schoolId}", $this->ttl, function () use ($schoolId) {
            return School::with(['province', 'district', 'zip'])
                ->withCount(['students', 'classes', 'teachers'])
                ->find($schoolId);
        });
    }

    /**
     * Flush all territorial and location caches.
     */
    public function flushTerritoryCache(): void
    {
        Cache::forget('system_provinces_all');
        Cache::forget('system_districts_all');
        Cache::forget('system_zips_all');

        foreach (Province::pluck('id') as $pId) {
            Cache::forget("system_districts_prov_{$pId}");
            Cache::forget("territory_stats_p{$pId}_d0_z0");
        }
        foreach (District::pluck('id') as $dId) {
            Cache::forget("system_zips_dist_{$dId}");
        }
    }

    /**
     * Flush cache for a specific school.
     */
    public function flushSchoolCache(int $schoolId): void
    {
        Cache::forget("school_details_{$schoolId}");
        $this->flushTerritoryCache();
    }
}
