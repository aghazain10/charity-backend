<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    /**
     * This route requires authentication
     */
    public function index(Request $request)
    {
        return response()->json([
            'message' => 'You have accessed the donations route!',
            'user' => $request->user(),
            'timestamp' => now()
        ]);
    }
    public function store(Request $request)
    {
        // Logic to validate and store a new donation
        $validatedData = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'amount' => 'required|numeric|min:1',
            'message' => 'nullable|string|max:500',
            'is_anonymous' => 'boolean',
        ]);
        $donation = $request->user()->donations()->create($validatedData);
        return response()->json(['message' => 'Donation created successfully', 'donation' => $donation]);
    }
    public function campaignDonations(Request $request, $campaignId)
    {
        // Logic to retrieve donations for a specific campaign
        $donations = \App\Models\Donation::where('campaign_id', $campaignId)->with('user:id,name')->get();
        return response()->json(['donations' => $donations]);
    }
}
