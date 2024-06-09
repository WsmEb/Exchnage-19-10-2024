<?php
namespace App\Http\Controllers;

use App\Models\Entreprise;
use Illuminate\Http\Request;

class EntrepriseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.is.connected');
    }
    public function edit($titre)
    {
        $entreprise = Entreprise::findOrFail($titre);
        return view('entreprise.edit', compact('entreprise'));
    }

    public function update(Request $request, $titre)
    {
        $entreprise = Entreprise::findOrFail($titre);
        $entreprise->update([
            'titre' => $request->input('titre'),
            'description' => $request->input('description')
            // Add more fields to update here if needed
        ]);

        return redirect()->back()->with('success', 'Entreprise details updated successfully!');
    }
}
