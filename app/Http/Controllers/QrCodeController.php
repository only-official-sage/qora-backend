<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QrCodeController extends Controller
{
    public function index()
    {
        $qrCodes = QrCode::where('admin_id', auth()->id())->with(['section', 'table'])->get();

        return response()->json($qrCodes);
    }

    public function show($id)
    {
        $qrCode = QrCode::where('admin_id', auth()->id())->with(['section', 'table'])->findOrFail($id);

        return response()->json($qrCode);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_id' => 'nullable|string|exists:sections,id',
            'table_id' => 'nullable|string|exists:tables,id',
        ]);

        if (! $validated['section_id'] && ! $validated['table_id']) {
            return response()->json(['message' => 'Either section_id or table_id is required'], 422);
        }

        $adminId = auth()->id();

        if ($validated['section_id']) {
            $exists = QrCode::where('admin_id', $adminId)
                ->where('section_id', $validated['section_id'])
                ->whereNull('table_id')
                ->exists();
            if ($exists) {
                return response()->json(['message' => 'QR code for this section already exists'], 422);
            }
        }

        $code = strtoupper(Str::random(8));

        $qrCode = QrCode::create([
            'admin_id' => $adminId,
            'section_id' => $validated['section_id'] ?? null,
            'table_id' => $validated['table_id'] ?? null,
            'code' => $code,
        ]);

        return response()->json($qrCode, 201);
    }

    public function destroy($id)
    {
        $qrCode = QrCode::where('admin_id', auth()->id())->findOrFail($id);

        if ($qrCode->path) {
            Storage::disk('local')->delete($qrCode->path);
        }

        $qrCode->delete();

        return response()->json(null, 204);
    }

    public function download($id)
    {
        $qrCode = QrCode::where('admin_id', auth()->id())->findOrFail($id);

        if ($qrCode->path && Storage::disk('local')->exists($qrCode->path)) {
            return Storage::disk('local')->download($qrCode->path);
        }

        return response()->json(['message' => 'QR code image not yet generated'], 404);
    }
}
