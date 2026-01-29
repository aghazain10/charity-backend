<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    /**
     * Display a listing of the campaigns.
     */
    public function index()
    {
        // Return all campaigns (public)
        $campaigns = Campaign::with('user:id,name')->get();
        return response()->json([
            'success' => true,
            'data' => $campaigns
        ]);
    }

    /**
     * Store a newly created campaign in storage.
     */
    public function store(Request $request)
    {


        try {
            // Check what fields your database expects
            $campaign = $request->user()->campaigns()->create([
                'title' => $request->title,
                'description' => $request->description,
                'target_amount' => $request->target_amount, // or 'target_amount' => $request->target_amount
                'current_amount' => 0,
                'deadline' => $request->deadline,
                'featured_image' => $request->featured_image,
                'is_featured' => $request->is_featured ?? false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Campaign created successfully',
                'data' => $campaign
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create campaign',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified campaign.
     */
    public function show($id)
    {
        $campaign = Campaign::with('user:id,name')->find($id);

        if (!$campaign) {
            return response()->json([
                'success' => false,
                'message' => 'Campaign not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $campaign,
        ]);
    }

    /**
     * Update the specified campaign in storage.
     */
    public function update(Request $request, $id)
    {
        $campaign = Campaign::find($id);

        if (!$campaign) {
            return response()->json([
                'success' => false,
                'message' => 'Campaign not found'
            ], 404);
        }

        // Check if user owns the campaign
        if ($request->user()->id !== $campaign->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this campaign'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'goal_amount' => 'numeric|min:0',
            'end_date' => 'date|after:today',
            'featured_image' => 'nullable|url',
            'status' => 'in:active,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $campaign->update($request->only([
            'title',
            'description',
            'goal_amount',
            'end_date',
            'featured_image',
            'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Campaign updated successfully',
            'data' => $campaign
        ]);
    }

    /**
     * Remove the specified campaign from storage.
     */
    public function destroy(Request $request, $id)
    {
        $campaign = Campaign::find($id);

        if (!$campaign) {
            return response()->json([
                'success' => false,
                'message' => 'Campaign not found'
            ], 404);
        }

        // Check if user owns the campaign
        if ($request->user()->id !== $campaign->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this campaign'
            ], 403);
        }

        $campaign->delete();

        return response()->json([
            'success' => true,
            'message' => 'Campaign deleted successfully'
        ]);
    }
    public function featured()
    {
        $featuredCampaigns = Campaign::where('is_featured', true)->with('user:id,name')->get();

        return response()->json([
            'success' => true,
            'data' => $featuredCampaigns
        ]);
    }
    public function indexPublic()
    {
        // Return all campaigns (public)
        $campaigns = Campaign::with('user:id,name')->get();
        return response()->json([
            'success' => true,
            'data' => $campaigns
        ]);
    }

    public function showPublic($id)
    {
        $campaign = Campaign::with('user:id,name')->find($id);

        if (!$campaign) {
            return response()->json([
                'success' => false,
                'message' => 'Campaign not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $campaign
        ]);
    }
}
