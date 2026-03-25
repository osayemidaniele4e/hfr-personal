<?php

namespace App\Http\Controllers;

use App\Services\FacilityCompletenessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FacilityCompletenessController extends Controller
{
    /**
     * Public completeness for the facility finder UI (no API key).
     */
    public function show(Request $request, $facilityId): JsonResponse
    {
        if (!is_numeric($facilityId) || (int) $facilityId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid facility id.',
            ], 422);
        }

        $result = app(FacilityCompletenessService::class)->computeForFacilityId((int) $facilityId);

        if ($result === null) {
            return response()->json([
                'success' => false,
                'message' => 'Facility not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ], 200);
    }
}
