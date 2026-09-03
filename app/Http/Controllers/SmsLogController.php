<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Services\Sms\SmsNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmsLogController extends Controller
{
    public function __construct(protected SmsNotificationService $smsNotifier) {}

    /**
     * Central de Mensagens — listagem principal com tabs.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $channel = $request->input('channel');
        $status = $request->input('status');

        $logs = \App\Models\SmsLog::query()
            ->with(['user', 'student'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($x) use ($search) {
                    $x->where('recipient_name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when($channel, fn ($q) => $q->where('channel', $channel))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        // KPIs para os stat cards
        $totalLogs = \App\Models\SmsLog::count();
        $totalSent = \App\Models\SmsLog::where('status', 'sent')->count();
        $totalFailed = \App\Models\SmsLog::where('status', 'failed')->count();
        $sentThisMonth = \App\Models\SmsLog::where('status', 'sent')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $deliveryRate = $totalLogs > 0 ? round(($totalSent / max($totalLogs, 1)) * 100) : 0;

        // Alunos para o formulário de envio individual (com encarregado com telefone)
        $students = Student::with('parent')
            ->whereHas('parent', fn ($q) => $q->whereNotNull('phone')->where('phone', '!=', ''))
            ->orWhereNotNull('emergency_phone')
            ->orderBy('first_name')
            ->limit(500)
            ->get(['id', 'first_name', 'last_name', 'student_number', 'parent_id', 'emergency_phone']);

        $fromNumber = config('services.sms.httpsms.from');
        $isSimulated = (bool) config('services.sms.simulate', false);
        $unreadInbox = \App\Models\SmsInbox::where('is_read', false)->count();

        return view('sms.index', [
            'logs' => $logs,
            'students' => $students,
            'stats' => [
                'total' => $totalLogs,
                'sent_this_month' => $sentThisMonth,
                'failed' => $totalFailed,
                'delivery_rate' => $deliveryRate,
            ],
            'enabled' => (bool) config('services.sms.enabled', false),
            'driver' => config('services.sms.driver', 'httpsms'),
            'from' => $fromNumber,
            'simulated' => $isSimulated,
            'unreadInbox' => $unreadInbox,
        ]);
    }

    /**
     * Reenviar um SMS a partir de um log anterior.
     */
    public function resend(\App\Models\SmsLog $log)
    {
        $this->authorize('view_students');

        $student = $log->student;

        $result = $this->smsNotifier->send(
            phone: $log->phone,
            message: $log->message,
            channel: $log->channel,
            context: ($log->context ?? 'resend').'-resend',
            student: $student,
            recipientName: $log->recipient_name,
        );

        if ($result['success'] ?? false) {
            return back()->with('success', 'SMS reenviado com sucesso.');
        }

        return back()->with('error', 'Falha ao reenviar: '.($result['error'] ?? 'desconhecido'));
    }

    /**
     * Apagar um registo do histórico (apaga apenas o log, não afecta o aluno).
     */
    public function destroy(\App\Models\SmsLog $log)
    {
        $this->authorize('view_students');

        $log->delete();

        return back()->with('success', 'Registo de SMS removido do histórico.');
    }
}