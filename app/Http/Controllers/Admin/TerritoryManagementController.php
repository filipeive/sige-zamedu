<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Zip;
use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TerritoryManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $selectedProvinceId = $request->input('province_id');
        $selectedDistrictId = $request->input('district_id');
        $selectedZipId = $request->input('zip_id');
        $activeTab = $request->input('tab', 'schools');

        // 1. Schools Query
        $schoolsQuery = School::with(['province', 'district', 'zip']);

        if ($selectedProvinceId) {
            $schoolsQuery->where('province_id', $selectedProvinceId);
        }
        if ($selectedDistrictId) {
            $schoolsQuery->where('district_id', $selectedDistrictId);
        }
        if ($selectedZipId) {
            $schoolsQuery->where('zip_id', $selectedZipId);
        }
        if ($search) {
            $schoolsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('principal_name', 'like', "%{$search}%");
            });
        }
        $schools = $schoolsQuery->latest()->get();

        // 2. ZIPs Query
        $zipsQuery = Zip::with(['district.province', 'schools']);
        if ($selectedDistrictId) {
            $zipsQuery->where('district_id', $selectedDistrictId);
        }
        if ($search) {
            $zipsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('headquarters', 'like', "%{$search}%");
            });
        }
        $zips = $zipsQuery->orderBy('name')->get();

        // 3. Districts Query
        $districtsQuery = District::with(['province', 'zips', 'schools']);
        if ($selectedProvinceId) {
            $districtsQuery->where('province_id', $selectedProvinceId);
        }
        if ($search) {
            $districtsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        $districts = $districtsQuery->orderBy('name')->get();

        // 4. Provinces Query
        $provincesQuery = Province::with(['districts', 'schools']);
        if ($search) {
            $provincesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        $provincesList = $provincesQuery->orderBy('name')->get();

        // Filter Dropdowns
        $provinces = Province::where('is_active', true)->orderBy('name')->get();
        $districtsDropdown = District::where('is_active', true)->orderBy('name')->get();
        $zipsDropdown = Zip::where('is_active', true)->orderBy('name')->get();

        return view('admin.territory.index', compact(
            'schools',
            'zips',
            'districts',
            'provincesList',
            'provinces',
            'districtsDropdown',
            'zipsDropdown',
            'search',
            'selectedProvinceId',
            'selectedDistrictId',
            'selectedZipId',
            'activeTab'
        ));
    }

    // --- SCHOOLS CRUD ---
    public function storeSchool(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:schools,code',
            'type' => 'required|string|in:Pública,Privada,Comunitária,public,private,community',
            'province_id' => 'required|exists:provinces,id',
            'district_id' => 'required|exists:districts,id',
            'zip_id' => 'nullable|exists:zips,id',
            'principal_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $validated['is_active'] = true;
        School::create($validated);
        app(\App\Services\SystemCacheService::class)->flushTerritoryCache();

        return redirect()->route('admin.territory.index', ['tab' => 'schools'])
            ->with('success', 'Nova escola registada com sucesso!');
    }

    public function updateSchool(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('schools', 'code')->ignore($school->id)],
            'type' => 'required|string|in:Pública,Privada,Comunitária,public,private,community',
            'province_id' => 'required|exists:provinces,id',
            'district_id' => 'required|exists:districts,id',
            'zip_id' => 'nullable|exists:zips,id',
            'principal_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $school->update($validated);
        app(\App\Services\SystemCacheService::class)->flushSchoolCache($school->id);

        return redirect()->route('admin.territory.index', ['tab' => 'schools'])
            ->with('success', "Escola {$school->name} atualizada com sucesso!");
    }

    public function toggleSchoolStatus(School $school)
    {
        $school->update(['is_active' => !$school->is_active]);
        app(\App\Services\SystemCacheService::class)->flushSchoolCache($school->id);

        $statusLabel = $school->is_active ? 'ativada' : 'desativada';
        return redirect()->back()->with('success', "Escola {$school->name} foi {$statusLabel}!");
    }

    // --- ZIPS CRUD ---
    public function showZip(Zip $zip)
    {
        $zip->load(['district.province', 'schools']);
        return view('admin.territory.zip_show', compact('zip'));
    }

    public function storeZip(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:zips,code',
            'district_id' => 'required|exists:districts,id',
            'headquarters' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = true;
        Zip::create($validated);
        app(\App\Services\SystemCacheService::class)->flushTerritoryCache();

        return redirect()->route('admin.territory.index', ['tab' => 'zips'])
            ->with('success', 'Nova ZIP criada com sucesso!');
    }

    public function updateZip(Request $request, Zip $zip)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('zips', 'code')->ignore($zip->id)],
            'district_id' => 'required|exists:districts,id',
            'headquarters' => 'nullable|string|max:255',
        ]);

        $zip->update($validated);
        app(\App\Services\SystemCacheService::class)->flushTerritoryCache();

        return redirect()->route('admin.territory.index', ['tab' => 'zips'])
            ->with('success', "ZIP {$zip->name} atualizada com sucesso!");
    }

    public function toggleZipStatus(Zip $zip)
    {
        $zip->update(['is_active' => !$zip->is_active]);
        app(\App\Services\SystemCacheService::class)->flushTerritoryCache();

        $statusLabel = $zip->is_active ? 'ativada' : 'desativada';
        return redirect()->back()->with('success', "ZIP {$zip->name} foi {$statusLabel}!");
    }

    // --- DISTRICTS CRUD ---
    public function showDistrict(District $district)
    {
        $district->load(['province', 'zips', 'schools']);
        return view('admin.territory.district_show', compact('district'));
    }

    public function storeDistrict(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:districts,code',
            'province_id' => 'required|exists:provinces,id',
        ]);

        $validated['is_active'] = true;
        District::create($validated);
        app(\App\Services\SystemCacheService::class)->flushTerritoryCache();

        return redirect()->route('admin.territory.index', ['tab' => 'districts'])
            ->with('success', 'Novo Distrito registado com sucesso!');
    }

    public function updateDistrict(Request $request, District $district)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('districts', 'code')->ignore($district->id)],
            'province_id' => 'required|exists:provinces,id',
        ]);

        $district->update($validated);
        app(\App\Services\SystemCacheService::class)->flushTerritoryCache();

        return redirect()->route('admin.territory.index', ['tab' => 'districts'])
            ->with('success', "Distrito {$district->name} atualizado com sucesso!");
    }

    public function toggleDistrictStatus(District $district)
    {
        $district->update(['is_active' => !$district->is_active]);
        app(\App\Services\SystemCacheService::class)->flushTerritoryCache();

        $statusLabel = $district->is_active ? 'ativado' : 'desativado';
        return redirect()->back()->with('success', "Distrito {$district->name} foi {$statusLabel}!");
    }

    // --- PROVINCES CRUD ---
    public function showProvince(Province $province)
    {
        $province->load(['districts', 'schools']);
        return view('admin.territory.province_show', compact('province'));
    }

    public function storeProvince(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:provinces,code',
        ]);

        $validated['is_active'] = true;
        Province::create($validated);
        app(\App\Services\SystemCacheService::class)->flushTerritoryCache();

        return redirect()->route('admin.territory.index', ['tab' => 'provinces'])
            ->with('success', 'Nova Província registada com sucesso!');
    }

    public function updateProvince(Request $request, Province $province)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('provinces', 'code')->ignore($province->id)],
        ]);

        $province->update($validated);
        app(\App\Services\SystemCacheService::class)->flushTerritoryCache();

        return redirect()->route('admin.territory.index', ['tab' => 'provinces'])
            ->with('success', "Província {$province->name} atualizada com sucesso!");
    }

    public function toggleProvinceStatus(Province $province)
    {
        $province->update(['is_active' => !$province->is_active]);
        app(\App\Services\SystemCacheService::class)->flushTerritoryCache();

        $statusLabel = $province->is_active ? 'ativada' : 'desativada';
        return redirect()->back()->with('success', "Província {$province->name} foi {$statusLabel}!");
    }
}
