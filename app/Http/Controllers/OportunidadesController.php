<?php

namespace App\Http\Controllers;

use App\Models\Oportunidade;
use Illuminate\Http\Request;

class OportunidadesController extends Controller
{
    public function index()
    {
        \Illuminate\Support\Facades\Gate::authorize('use-feature', 'oportunidades.manage');
        $oportunidades = Oportunidade::orderBy('created_at', 'desc')->paginate(10);
        return view('pages.oportunidades.index', [
            'title' => 'Oportunidades',
            'oportunidades' => $oportunidades,
        ]);
    }

    public function create()
    {
        \Illuminate\Support\Facades\Gate::authorize('use-feature', 'oportunidades.manage');
        return view('pages.oportunidades.create', [
            'title' => 'Nova Oportunidade',
            'oportunidade' => new Oportunidade(),
        ]);
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('use-feature', 'oportunidades.manage');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'source' => ['nullable', 'string', 'max:100'],
            'stage' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:open,won,lost'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'probability' => ['nullable', 'integer', 'between:0,100'],
            'next_action_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'score' => ['nullable', 'integer', 'min:0'],
        ]);

        Oportunidade::create($data);
        return redirect()->route('oportunidades.index')->with('success', 'Oportunidade criada com sucesso.');
    }

    public function edit(Oportunidade $oportunidade)
    {
        \Illuminate\Support\Facades\Gate::authorize('use-feature', 'oportunidades.manage');
        return view('pages.oportunidades.edit', [
            'title' => 'Editar Oportunidade',
            'oportunidade' => $oportunidade,
        ]);
    }

    public function update(Request $request, Oportunidade $oportunidade)
    {
        \Illuminate\Support\Facades\Gate::authorize('use-feature', 'oportunidades.manage');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'source' => ['nullable', 'string', 'max:100'],
            'stage' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:open,won,lost'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'probability' => ['nullable', 'integer', 'between:0,100'],
            'next_action_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'score' => ['nullable', 'integer', 'min:0'],
        ]);

        $oportunidade->fill($data);
        $oportunidade->save();
        return redirect()->route('oportunidades.index')->with('success', 'Oportunidade atualizada com sucesso.');
    }

    public function destroy(Oportunidade $oportunidade)
    {
        \Illuminate\Support\Facades\Gate::authorize('use-feature', 'oportunidades.manage');
        Oportunidade::destroy($oportunidade->id);
        return redirect()->route('oportunidades.index')->with('success', 'Oportunidade removida.');
    }
}
