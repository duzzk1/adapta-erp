<?php

namespace App\Http\Controllers;

use App\Jobs\ImportCallsCSV;
use App\Jobs\ImportLuggiaData;
use App\Models\ApiSetting;
use App\Models\ApiPermission;
use App\Models\User;
use App\Models\JobProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class IntegracoesController extends Controller
{
    public function index()
    {
        if (\Illuminate\Support\Facades\Gate::denies('use-feature', 'integrations.view')) {
            abort(403);
        }
        $integrations = [
            [
                'key' => 'luggia',
                'name' => 'Luggia API',
                'description' => 'Importa chamadas via API com autenticação básica.',
            ],
            [
                'key' => 'csv',
                'name' => 'CSV Local',
                'description' => 'Importa chamadas de um arquivo CSV em storage.',
            ],
        ];

        $saved = ApiSetting::get('luggia');
        $savedBaseUrl = $saved['base_url'] ?? null;
        $savedUser = $saved['user'] ?? null;

        // Fake API flags for UI behavior
        $fakeEnabled = filter_var(env('LUGGIA_FAKE_ENABLED', false), FILTER_VALIDATE_BOOL);
        $fakeUrl = env('LUGGIA_FAKE_URL');

        // Permissions management data
        $users = User::orderBy('name', 'asc')->get();
        $permissions = ApiPermission::all()->groupBy('api_key')->map(function ($group) {
            return $group->keyBy('user_id');
        });

        return view('pages.integracoes.index', [
            'integrations' => $integrations,
            'title' => 'Integrações',
            'savedBaseUrl' => $savedBaseUrl,
            'savedUser' => $savedUser,
            'fakeEnabled' => $fakeEnabled,
            'fakeUrl' => $fakeUrl,
            'users' => $users,
            'permissions' => $permissions,
        ]);
    }

    public function search(Request $request, string $api)
    {
        $featureKey = $api === 'luggia' ? 'integrations.luggia' : ($api === 'csv' ? 'integrations.csv' : 'integrations.view');
        if (Gate::denies('use-feature', $featureKey)) {
            return back()->with('error', 'Você não tem permissão para usar esta integração.');
        }
        switch ($api) {
            case 'luggia':
                $runId = (string) Str::uuid();
                $saved = ApiSetting::get('luggia') ?? [];
                $savedUrl = $saved['base_url'] ?? null;
                $savedUser = $saved['user'] ?? null;
                $savedPassEnc = $saved['pass_encrypted'] ?? null;

                $editUrl = $request->boolean('edit_url');
                $editCreds = $request->boolean('edit_creds');

                // Fake API override via ENV
                $fakeEnabled = filter_var(env('LUGGIA_FAKE_ENABLED', false), FILTER_VALIDATE_BOOL);
                $fakeUrl = env('LUGGIA_FAKE_URL');

                // If we have everything saved and user isn't editing, use saved creds
                if ($savedUrl && $savedUser && $savedPassEnc && !$editUrl && !$editCreds) {
                    $pass = Crypt::decryptString($savedPassEnc);
                    ImportLuggiaData::dispatch($savedUrl, $savedUser, $pass, $runId);
                    return back()->with('success', 'Importação da Luggia iniciada usando URL e credenciais salvas.')->with('run_id', $runId);
                }

                // Validate only what's being edited or missing
                $rules = [];
                // In fake mode, base_url can be skipped (will be overridden)
                if ((!$fakeEnabled || !$fakeUrl) && (!$savedUrl || $editUrl)) {
                    $rules['base_url'] = ['required', 'url'];
                }
                // In fake mode, credentials are optional
                if ((!$fakeEnabled || !$fakeUrl) && (!$savedUser || !$savedPassEnc || $editCreds)) {
                    $rules['user'] = ['required', 'string'];
                    $rules['pass'] = ['required', 'string'];
                }

                $validated = $request->validate($rules);

                // Persist updates
                if (array_key_exists('base_url', $validated)) {
                    ApiSetting::set('luggia', ['base_url' => $validated['base_url']]);
                }
                if (array_key_exists('user', $validated)) {
                    ApiSetting::set('luggia', [
                        'user' => $validated['user'],
                        'pass_encrypted' => Crypt::encryptString($validated['pass']),
                    ]);
                }

                $useUrl = $validated['base_url'] ?? $savedUrl;
                if ($fakeEnabled && $fakeUrl) {
                    // Ensure absolute URL for fake API
                    if (str_starts_with($fakeUrl, 'http://') || str_starts_with($fakeUrl, 'https://')) {
                        $useUrl = rtrim($fakeUrl, '/');
                    } else {
                        $useUrl = rtrim(url($fakeUrl), '/');
                    }
                }
                $useUser = $validated['user'] ?? $savedUser ?? 'fake';
                $usePass = isset($validated['pass']) ? $validated['pass'] : ($savedPassEnc ? Crypt::decryptString($savedPassEnc) : 'fake');

                if ($useUrl && $useUser && $usePass) {
                    ImportLuggiaData::dispatch($useUrl, $useUser, $usePass, $runId);
                    return back()->with('success', 'Importação da Luggia iniciada. Configurações atualizadas quando informado.')->with('run_id', $runId);
                }

                return back()->with('error', 'Dados insuficientes para iniciar a importação.');

            case 'csv':
                $runId = (string) Str::uuid();
                ImportCallsCSV::dispatch($runId);
                return back()->with('success', 'Importação do CSV iniciada.')->with('run_id', $runId);

            default:
                return back()->with('error', 'Integração desconhecida.');
        }
    }

    public function progress(string $runId)
    {
        $progress = JobProgress::query()->where('run_id', $runId)->first();
        if (!$progress) {
            return response()->json(['status' => 'unknown']);
        }
        return response()->json([
            'status' => $progress->status,
            'current' => $progress->current,
            'total' => $progress->total,
            'message' => $progress->message,
            'updated_at' => $progress->updated_at,
        ]);
    }

    public function monitor()
    {
        if (\Illuminate\Support\Facades\Gate::denies('use-feature', 'integrations.monitor')) {
            abort(403);
        }
        $runs = JobProgress::orderByDesc('updated_at')->limit(20)->get();
        return view('pages.integracoes.monitor', [
            'title' => 'Fila',
            'runs' => $runs,
        ]);
    }

    public function updatePermissions(Request $request)
    {
        $data = $request->input('permissions', []);

        foreach ($data as $apiKey => $users) {
            foreach ($users as $userId => $value) {
                $canUse = (bool) $value;
                ApiPermission::updateOrCreate(
                    ['user_id' => (int) $userId, 'api_key' => $apiKey],
                    ['can_use' => $canUse]
                );
            }
        }

        return back()->with('success', 'Permissões atualizadas.');
    }
}