<?php

namespace App\Http\Middleware;

use App\Models\School;
use App\Services\SchoolContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class IdentifySchool
{
    /**
     * Handle an incoming request and initialize the SchoolContext.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $schoolId = null;

        if ($user = $request->user()) {
            $schoolId = $user->school_id;
        }

        if (!$schoolId && $request->hasHeader('X-School-Id')) {
            $schoolId = (int) $request->header('X-School-Id');
        }

        if (!$schoolId) {
            $schoolId = session()->has('active_school_id') ? (int) session('active_school_id') : null;
        }

        if (Schema::hasTable('schools')) {
            if (!$schoolId) {
                $schoolId = School::value('id');
            }

            if ($schoolId && School::where('id', $schoolId)->exists()) {
                SchoolContext::setSchoolId((int) $schoolId);
            } else {
                SchoolContext::clear();
            }
        }

        return $next($request);
    }
}
