<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::where('admin_id', auth()->id())->get();

        return response()->json($assets);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'image' => 'required|string', // base64
        ]);

        $base64String = $validated['image'];
        if (str_contains($base64String, 'base64,')) {
            $base64String = explode('base64,', $base64String)[1];
        }

        $imageData = base64_decode($base64String, true);
        if ($imageData === false) {
            return response()->json(['message' => 'Invalid base64 image'], 422);
        }

        $extension = 'png';
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_buffer($finfo, $imageData);
            finfo_close($finfo);
            if ($mime === 'image/jpeg') {
                $extension = 'jpg';
            } elseif ($mime === 'image/png') {
                $extension = 'png';
            } elseif ($mime === 'image/gif') {
                $extension = 'gif';
            }
        }

        $folder = match ($validated['type']) {
            'flyer' => 'flyers',
            'ig_post' => 'ig_posts',
            default => 'logos',
        };

        $filename = $folder.'/'.Str::uuid().'.'.$extension;
        Storage::disk('local')->put($filename, $imageData);

        $asset = Asset::create([
            'admin_id' => auth()->id(),
            'type' => $validated['type'],
            'path' => $filename,
            'mime_type' => $mime ?? 'image/png',
            'size' => strlen($imageData),
        ]);

        return response()->json($asset, 201);
    }

    public function destroy($id)
    {
        $asset = Asset::where('admin_id', auth()->id())->findOrFail($id);

        Storage::disk('local')->delete($asset->path);
        $asset->delete();

        return response()->json(null, 204);
    }
}
