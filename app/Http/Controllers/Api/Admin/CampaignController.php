<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    /**
     * Create a new campaign
     * Returns: function description only, no implementation
     */
    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'audience' => 'required|in:VIP,ROOM,TABLE,BAR,CUSTOM',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['admin_id'] = auth('admin')->user()->id;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('campaigns', 'public');
        }

        $campaign = Campaign::create($validated);

        return response()->json($campaign, 201);
    }

    /**
     * Edit an existing campaign
     * Returns: function description only, no implementation
     */
    public function edit(int $id): JsonResponse
    {
        return response()->json([
            'message' => 'Campaign edit endpoint',
            'description' => "This endpoint updates campaign with ID {$id}. Accepts parameters: name, description, start_date, end_date, budget, target_audience, status, etc.",
            'status' => 'descriptor_only'
        ]);
    }

    /**
     * Delete a campaign
     */
    public function destroy(int $id): JsonResponse
    {
        $campaign = Campaign::findOrFail($id);

        if ($campaign->image) {
            Storage::disk('public')->delete($campaign->image);
        }

        $campaign->delete();

        return response()->json(null, 204);
    }
}
