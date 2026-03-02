<?php

namespace App\Http\Controllers;

use App\Services\ApiGamesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    protected $apiGamesService;

    public function __construct(ApiGamesService $apiGamesService)
    {
        $this->apiGamesService = $apiGamesService;
    }

    public function checkUsername(Request $request, $game_code)
    {
        $request->validate([
            'user_id' => 'required|string',
            'zone_id' => 'sometimes|string|nullable',
            'server_id' => 'sometimes|string|nullable',
        ]);

        $userId = $request->input('user_id');
        $zoneId = $request->input('zone_id') ?? $request->input('server_id');

        try {
            $data = $this->apiGamesService->checkUsername($game_code, $userId, $zoneId);

            if (!empty($data) && isset($data['username'])) {
                return response()->json([
                    'success' => true,
                    'username' => $data['username'],
                    'user_id' => $userId,
                    'zone_id' => $zoneId,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $data['message'] ?? 'User ID tidak valid atau tidak ditemukan.'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error checking username for ' . $game_code . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat memverifikasi User ID. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Check username with game_code from request body/query
     * Supports alternative field names to avoid WAF blocking
     */
    public function checkUsernameFromRequest(Request $request)
    {
        // Get values from multiple possible field names
        $gameCode = $request->input('game_code') ?? $request->input('game') ?? $request->input('g');
        $userId = $request->input('user_id') ?? $request->input('uid') ?? $request->input('id') ?? $request->input('u');
        $zoneId = $request->input('zone_id') ?? $request->input('server_id') ?? $request->input('zone') ?? $request->input('z');

        // Validation
        if (empty($gameCode)) {
            return response()->json([
                'success' => false,
                'message' => 'Game code is required'
            ], 422);
        }

        if (empty($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'User ID is required'
            ], 422);
        }

        try {
            $data = $this->apiGamesService->checkUsername($gameCode, $userId, $zoneId);

            if (!empty($data) && isset($data['username'])) {
                return response()->json([
                    'success' => true,
                    'username' => $data['username'],
                    'user_id' => $userId,
                    'zone_id' => $zoneId,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $data['message'] ?? 'User ID tidak valid atau tidak ditemukan.'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error checking username for ' . $gameCode . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat memverifikasi User ID. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Check username with parameters in URL path (WAF bypass)
     * Route: /v/{game}/{uid}/{zone?}
     */
    public function checkUsernameFromPath(Request $request, string $game, string $uid, ?string $zone = null)
    {
        try {
            $data = $this->apiGamesService->checkUsername($game, $uid, $zone);

            if (!empty($data) && isset($data['username'])) {
                return response()->json([
                    'success' => true,
                    'username' => $data['username'],
                    'user_id' => $uid,
                    'zone_id' => $zone,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $data['message'] ?? 'User ID tidak valid atau tidak ditemukan.'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error checking username for ' . $game . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat memverifikasi User ID. Silakan coba lagi.'
            ], 500);
        }
    }
}
