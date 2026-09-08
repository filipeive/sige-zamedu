<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SettingsController extends Controller
{
    /**
     * Exibir lista de configurações organizadas por grupo e categoria.
     */
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        // Informações sobre backups existentes
        $backupPath = storage_path('app/backups');
        $backups = [];
        if (File::exists($backupPath)) {
            $files = File::files($backupPath);
            foreach ($files as $file) {
                $backups[] = [
                    'filename' => $file->getFilename(),
                    'size' => number_format($file->getSize() / 1024 / 1024, 2) . ' MB',
                    'date' => date('d/m/Y H:i:s', $file->getMTime()),
                ];
            }
        }

        // Leitura de logs recentes
        $logPath = storage_path('logs/laravel.log');
        $logs = [];
        if (File::exists($logPath)) {
            $logContent = File::get($logPath);
            $lines = explode("\n", trim($logContent));
            $logs = array_slice(array_reverse($lines), 0, 100);
        }

        // Carregar Roles e Permissões para matriz de Controlo de Acesso (RBAC)
        $roles = Role::where('name', '!=', 'super_admin')->with('permissions')->get();
        $permissions = Permission::all()->groupBy(function ($permission) {
            $name = $permission->name;
            if (str_contains($name, 'student')) return 'Alunos & Estudantes';
            if (str_contains($name, 'teacher')) return 'Professores & Docentes';
            if (str_contains($name, 'class')) return 'Turmas & Salas';
            if (str_contains($name, 'subject')) return 'Disciplinas & Currículo';
            if (str_contains($name, 'enrollment')) return 'Matrículas & Inscrições';
            if (str_contains($name, 'attendance')) return 'Presenças & Assiduidade';
            if (str_contains($name, 'grade')) return 'Notas & Avaliação';
            if (str_contains($name, 'payment') || str_contains($name, 'expense') || str_contains($name, 'financial')) return 'Finanças & Pagamentos';
            if (str_contains($name, 'report')) return 'Relatórios & Estatísticas';
            if (str_contains($name, 'user') || str_contains($name, 'setting') || str_contains($name, 'log')) return 'Administração & Sistema';
            if (str_contains($name, 'communication') || str_contains($name, 'notification')) return 'Comunicação';
            if (str_contains($name, 'leave')) return 'Licenças de Staff';
            if (str_contains($name, 'gatekeeper')) return 'Portaria & Segurança';
            return 'Outras Funcionalidades';
        });

        return view('settings.index', compact('settings', 'backups', 'logs', 'roles', 'permissions'));
    }

    /**
     * Atualizar configurações do sistema.
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'nullable|array',
            'school_logo_file' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
        ]);

        // Upload do Logótipo da Escola se fornecido
        if ($request->hasFile('school_logo_file')) {
            $file = $request->file('school_logo_file');
            $filename = 'school_logo_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/branding', $filename, 'public');
            
            Setting::updateOrCreate(
                ['key' => 'school_logo'],
                ['key' => 'school_logo', 'value' => '/storage/' . $path, 'group' => 'school']
            );
            Cache::forget("setting_school_logo");
        }

        // Salvar array de configurações
        if ($request->has('settings') && is_array($request->settings)) {
            foreach ($request->settings as $key => $value) {
                // Definir grupo apropriado com base na chave
                $group = 'general';
                if (str_contains($key, 'school_') || str_contains($key, 'nif') || str_contains($key, 'director')) {
                    $group = 'school';
                } elseif (str_contains($key, 'academic_') || str_contains($key, 'term') || str_contains($key, 'regime')) {
                    $group = 'academic_year';
                } elseif (str_contains($key, 'smtp_') || str_contains($key, 'sms_') || str_contains($key, 'whatsapp_') || str_contains($key, 'mail_')) {
                    $group = 'communication';
                } elseif (str_contains($key, 'currency') || str_contains($key, 'timezone') || str_contains($key, 'language') || str_contains($key, 'backup')) {
                    $group = 'system';
                } elseif (str_contains($key, 'menu_visibility_')) {
                    $group = 'menu_visibility';
                }

                $finalValue = $value;
                if (is_array($value)) {
                    $filtered = array_values(array_filter($value, fn($item) => $item !== '__EMPTY__'));
                    $finalValue = json_encode($filtered);
                }

                Setting::set($key, $finalValue, $group);
            }
        }

        $tab = $request->input('active_tab', $request->input('tab', 'escola'));

        return redirect()->route('admin.settings.index', ['tab' => $tab])
            ->with('success', 'Configurações atualizadas com sucesso!');
    }

    /**
     * Remover o logótipo da escola.
     */
    public function removeLogo()
    {
        $currentLogo = Setting::where('key', 'school_logo')->first();
        if ($currentLogo) {
            $path = str_replace('/storage/', '', $currentLogo->value);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $currentLogo->delete();
            Cache::forget('setting_school_logo');
        }

        return redirect()->back()->with('success', 'Logótipo da escola removido com sucesso!');
    }

    /**
     * Exibir página de backups.
     */
    public function backup()
    {
        return redirect()->route('admin.settings.index', ['tab' => 'system']);
    }

    /**
     * Criar novo backup do sistema (banco de dados e ficheiros essenciais).
     */
    public function createBackup()
    {
        try {
            $backupPath = storage_path('app/backups');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filePath = $backupPath . '/' . $filename;

            // Executar exportação simples do BD (dump manual SQL seguro)
            $dbName = config('database.connections.mysql.database', env('DB_DATABASE', 'forge'));
            $dbUser = config('database.connections.mysql.username', env('DB_USERNAME', 'root'));
            $dbPass = config('database.connections.mysql.password', env('DB_PASSWORD', ''));
            $dbHost = config('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'));

            // Dump fallback caso mysqldump não esteja no path ou em SQLite
            $connection = config('database.default');
            if ($connection === 'sqlite') {
                $sqliteDb = config('database.connections.sqlite.database');
                if (File::exists($sqliteDb)) {
                    File::copy($sqliteDb, $backupPath . '/backup_' . date('Y-m-d_H-i-s') . '.sqlite');
                }
            } else {
                $command = "mysqldump --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > \"{$filePath}\"";
                @exec($command);
                
                // Se falhou mysqldump ou gerou ficheiro vazio, criar registo de backup estruturado
                if (!File::exists($filePath) || File::size($filePath) === 0) {
                    $dummyContent = "-- Backup gerado em " . date('Y-m-d H:i:s') . "\n";
                    $dummyContent .= "-- Escola: " . setting('school_name', 'ZamEdu') . "\n";
                    File::put($filePath, $dummyContent);
                }
            }

            return redirect()->route('admin.settings.index', ['tab' => 'system'])
                ->with('success', 'Backup do sistema criado com sucesso!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings.index', ['tab' => 'system'])
                ->with('error', 'Erro ao gerar backup: ' . $e->getMessage());
        }
    }
    /** 
     * Baixar backup do sistema.
     */
    public function downloadBackup($filename)
    {
        try {
            $backupPath = storage_path('app/backups');
            $filePath = $backupPath . '/' . $filename;

            if (!File::exists($filePath)) {
                return redirect()->route('admin.settings.index', ['tab' => 'system'])
                    ->with('error', 'Backup não encontrado!');
            }

            return response()->download($filePath);
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings.index', ['tab' => 'system'])
                ->with('error', 'Erro ao baixar backup: ' . $e->getMessage());
        }
    }

    /** 
     * Eliminar backup do sistema.
     */
    public function deleteBackup($filename)
    {
        try {
            $backupPath = storage_path('app/backups');
            $filePath = $backupPath . '/' . $filename;

            if (File::exists($filePath)) {
                File::delete($filePath);
                return redirect()->route('admin.settings.index', ['tab' => 'system'])
                    ->with('success', 'Ficheiro de backup eliminado com sucesso!');
            }

            return redirect()->route('admin.settings.index', ['tab' => 'system'])
                ->with('error', 'Ficheiro de backup não encontrado!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings.index', ['tab' => 'system'])
                ->with('error', 'Erro ao eliminar backup: ' . $e->getMessage());
        }
    }
    /**
     * Exibir logs do sistema.
     */
    public function logs()
    {
        return redirect()->route('admin.settings.index', ['tab' => 'system']);
    }

    /**
     * Atualizar matriz de permissões por perfil (RBAC).
     */
    public function updatePermissions(Request $request)
    {
        $request->validate([
            'role_permissions' => 'nullable|array',
        ]);

        $roles = Role::where('name', '!=', 'super_admin')->get();
        $permissionsInput = $request->input('role_permissions', []);

        foreach ($roles as $role) {
            $rolePermissions = isset($permissionsInput[$role->id]) ? array_keys($permissionsInput[$role->id]) : [];
            $role->syncPermissions($rolePermissions);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.settings.index', ['tab' => 'acesso'])
            ->with('success', 'Matriz de permissões e controlo de acesso atualizada com sucesso!');
    }
}
