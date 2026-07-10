<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::with('admin')->latest()->paginate(15);
        return response()->json($campaigns);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'audience' => 'required|in:VIP,ROOM,TABLE,BAR,CUSTOM',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['admin_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('campaigns', 'public');
            $validated['image'] = $path;
        }

        $campaign = Campaign::create($validated);

        return response()->json($campaign, 201);
    }

    public function show(Campaign $campaign)
    {
        $campaign->load('admin');
        return response()->json($campaign);
    }

    public function update(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'audience' => 'sometimes|in:VIP,ROOM,TABLE,BAR,CUSTOM',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($campaign->image) {
                Storage::disk('public')->delete($campaign->image);
            }
            $path = $request->file('image')->store('campaigns', 'public');
            $validated['image'] = $path;
        }

        $campaign->update($validated);
        $campaign->refresh();

        return response()->json($campaign);
    }

    public function destroy(Campaign $campaign)
    {
        $this->authorize('delete', $campaign);

        if ($campaign->image) {
            Storage::disk('public')->delete($campaign->image);
        }

        $campaign->delete();

        return response()->json(null, 204);
    }
}
