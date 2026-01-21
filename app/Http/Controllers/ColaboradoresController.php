<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ColaboradoresController extends Controller
{
    public function index()
    {
        \Illuminate\Support\Facades\Gate::authorize('use-feature', 'colaboradores.manage');
        $collaborators = Collaborator::orderBy('name', 'asc')->paginate(10);
        return view('pages.colaboradores.index', [
            'title' => 'Colaboradores',
            'collaborators' => $collaborators,
        ]);
    }

    public function create()
    {
        \Illuminate\Support\Facades\Gate::authorize('use-feature', 'colaboradores.manage');
        return view('pages.colaboradores.create', [
            'title' => 'Novo Colaborador',
            'collaborator' => new Collaborator(),
        ]);
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('use-feature', 'colaboradores.manage');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:collaborators,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:active,inactive'],
            'hired_at' => ['nullable', 'date'],
        ]);

        // Normalize hired_at to a proper datetime (supports Y-m-d and d/m/Y)
        if (!empty($data['hired_at'])) {
            try {
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data['hired_at'])) {
                    $data['hired_at'] = Carbon::createFromFormat('d/m/Y', $data['hired_at'])->startOfDay();
                } else {
                    $data['hired_at'] = Carbon::createFromFormat('Y-m-d', $data['hired_at'])->startOfDay();
                }
            } catch (\Exception $e) {
                $data['hired_at'] = null;
            }
        }

        Collaborator::create($data);
        return redirect()->route('colaboradores.index')->with('success', 'Colaborador criado com sucesso.');
    }

    public function edit(Collaborator $colaborador)
    {
        \Illuminate\Support\Facades\Gate::authorize('use-feature', 'colaboradores.manage');
        return view('pages.colaboradores.edit', [
            'title' => 'Editar Colaborador',
            'collaborator' => $colaborador,
        ]);
    }

    public function update(Request $request, Collaborator $colaborador)
    {
        \Illuminate\Support\Facades\Gate::authorize('use-feature', 'colaboradores.manage');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:collaborators,email,' . $colaborador->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:active,inactive'],
            'hired_at' => ['nullable', 'date'],
        ]);

        // Normalize hired_at to a proper datetime (supports Y-m-d and d/m/Y)
        if (!empty($data['hired_at'])) {
            try {
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data['hired_at'])) {
                    $data['hired_at'] = Carbon::createFromFormat('d/m/Y', $data['hired_at'])->startOfDay();
                } else {
                    $data['hired_at'] = Carbon::createFromFormat('Y-m-d', $data['hired_at'])->startOfDay();
                }
            } catch (\Exception $e) {
                $data['hired_at'] = null;
            }
        }

        $colaborador->fill($data);
        $colaborador->save();
        return redirect()->route('colaboradores.index')->with('success', 'Colaborador atualizado com sucesso.');
    }

    public function destroy(Collaborator $colaborador)
    {
        \Illuminate\Support\Facades\Gate::authorize('use-feature', 'colaboradores.manage');
        Collaborator::destroy($colaborador->id);
        return redirect()->route('colaboradores.index')->with('success', 'Colaborador removido.');
    }
}
