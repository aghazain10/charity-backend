<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProtectedController extends Controller
{
    /**
     * This route requires authentication
     */
    public function index(Request $request)
    {
        return response()->json([
            'message' => 'You have accessed a protected route!',
            'user' => $request->user(),
            'timestamp' => now()
        ]);
    }
}
