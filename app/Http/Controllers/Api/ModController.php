<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mod;
use App\Services\ModService;
use Illuminate\Http\Request;

class ModController extends Controller
{
    public function __construct(protected ModService $modService) {}

    public function getMods()
    {
        return response()->json($this->modService->getAllMods());
    }

    public function createMod(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:mods,name',
            'mod_id' => 'required|string|unique:mods,mod_id',
            'description' => 'nullable|string'
        ]);

        $mod = $this->modService->createMod($validated, $request->user());
        return response()->json($mod, 201);
    }

    public function updateMod(Request $request, Mod $id_mod)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|unique:mods,name,' . $id_mod->id_mod . ',id_mod',
            'description' => 'nullable|string'
        ]);

        $mod = $this->modService->updateMod($id_mod, $validated, $request->user());
        return response()->json($mod);
    }

    public function deleteMod(Request $request, Mod $id_mod)
    {
        $this->modService->deleteMod($id_mod, $request->user());
        return response()->json(['message' => 'Мод удален']);
    }
}
