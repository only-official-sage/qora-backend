<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $campaigns = Campaign::where('admin_id', $request->user()->getKey())->get();

        return response()->json($campaigns);
    }


//     public function index(Request $request)
// {
//     dd(auth()->id());
// }
    public function show(Request $request, $id)
    {
        $campaign = Campaign::where('admin_id', $request->user()->getKey())->findOrFail($id);

        return response()->json($campaign);
    }

    public function create(Request $request): JsonResponse
    {
        // $validated = $request->validate([
        //     'name' => 'required|string|max:255',
        //     'audience' => 'required|in:VIP,ROOM,TABLE,BAR,CUSTOM',
        //     'description' => 'nullable|string',
        //     'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        // ]);
         $validated = $request->validate([
    'name' => 'required|string|max:255',
    'audience' => 'required|in:ALL_CUSTOMERS,REPEAT_CUSTOMERS,RECENT_CUSTOMERS',
    'description' => 'nullable|string',
    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
]);

        // $validated['admin_id'] = auth()->id();

        $validated['admin_id'] = $request->user()->getKey();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('campaigns', 'public');
        }

        $campaign = Campaign::create($validated);

        return response()->json($campaign, 201);
    }

    public function edit(Request $request, $id): JsonResponse
    {
        $campaign = Campaign::where('admin_id', $request->user()->getKey())->findOrFail($id);

        // $validated = $request->validate([
        //     'name' => 'sometimes|string|max:255',
        //     'audience' => 'sometimes|in:VIP,ROOM,TABLE,BAR,CUSTOM',
        //     'description' => 'nullable|string',
        //     'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        // ]);

         $validated = $request->validate([
    'name' => 'required|string|max:255',
    'audience' => 'required|in:ALL_CUSTOMERS,REPEAT_CUSTOMERS,RECENT_CUSTOMERS',
    'description' => 'nullable|string',
    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
]);

        if ($request->hasFile('image')) {
            if ($campaign->image) {
                Storage::disk('public')->delete($campaign->image);
            }
            $validated['image'] = $request->file('image')->store('campaigns', 'public');
        }

        $campaign->update($validated);

        return response()->json($campaign);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $campaign = Campaign::where('admin_id', $request->user()->getKey())->findOrFail($id);

        if ($campaign->image) {
            Storage::disk('public')->delete($campaign->image);
        }

        $campaign->delete();

        return response()->json(null, 204);
    }
}
