<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use Illuminate\Http\Request;

class BusController extends Controller
{
    public function index()
    {
        $buses = Bus::paginate(10);
        return view('manager.buses.index', compact('buses'));
    }

    public function create()
    {
        return view('manager.buses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|unique:buses',
            'name' => 'required|string|max:255|unique:buses',
            'places' => 'required|integer|min:1|max:100',
        ]);

        Bus::create($validated);

        return redirect()->route('manager.buses.index')->with('success', 'Автобус успешно добавлен');
    }

    public function edit(Bus $bus)
    {
        return view('manager.buses.edit', compact('bus'));
    }

    public function update(Request $request, Bus $bus)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:buses,name,' . $bus->id,
            'places' => 'required|integer|min:1|max:100',
        ]);

        $bus->update($validated);

        return redirect()->route('manager.buses.index')->with('success', 'Автобус обновлен');
    }

    public function destroy(Bus $bus)
    {
        $bus->delete();
        return redirect()->route('manager.buses.index')->with('success', 'Автобус удален');
    }
}
