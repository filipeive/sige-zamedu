<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\Sms\SmsNotificationService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function __construct(protected SmsNotificationService $smsNotifier) {}

    /**
     * Enviar uma mensagem SMS ao encarregado do aluno (a partir do perfil do aluno).
     * Auditada automaticamente via SmsNotificationService → SmsLog.
     */
    public function sendToGuardian(Request $request, Student $student)
    {
        $this->authorize('view_students');

        $validated = $request->validate([
            'message' => 'required|string|max:480',
            'phone' => 'nullable|string|max:20',
        ]);

        $parent = $student->parent;

        $phone = $validated['phone']
            ?? $parent?->phone
            ?? $student->emergency_phone;

        if (empty($phone)) {
            return back()->with('error', 'Não foi possível identificar um telefone para o encarregado.');
        }

        $message = str_replace(
            ['{aluno}', '{nome_aluno}', '{escola}'],
            [$student->full_name, $student->full_name, config('app.name')],
            $validated['message']
        );

        $result = $this->smsNotifier->send(
            phone: $phone,
            message: $message,
            channel: 'guardian',
            context: 'manual-guardian',
            student: $student,
            recipientName: $parent?->full_name,
        );

        if ($result['success'] ?? false) {
            return back()->with('success', 'Mensagem enviada por SMS ao encarregado. Auditada em /sms.');
        }

        return back()->with('error', 'Falha ao enviar SMS: '.($result['error'] ?? 'desconhecido'));
    }

    /**
     * Envio individual a partir da Central de Mensagens.
     */
    public function sendSingle(Request $request)
    {
        $this->authorize('view_students');

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'message' => 'required|string|max:480',
            'phone' => 'nullable|string|max:20',
        ]);

        $student = Student::with('parent')->findOrFail($validated['student_id']);
        $parent = $student->parent;

        $phone = $validated['phone']
            ?? $parent?->phone
            ?? $student->emergency_phone;

        if (empty($phone)) {
            return back()->with('error', 'O aluno não tem telefone de encarregado nem de emergência.');
        }

        $message = str_replace(
            ['{aluno}', '{nome_aluno}', '{escola}'],
            [$student->full_name, $student->full_name, config('app.name')],
            $validated['message']
        );

        $result = $this->smsNotifier->send(
            phone: $phone,
            message: $message,
            channel: 'guardian',
            context: 'central-single',
            student: $student,
            recipientName: $parent?->full_name,
        );

        if ($result['success'] ?? false) {
            return redirect()->route('sms.index')->with('success', 'SMS enviado com sucesso.');
        }

        return back()->with('error', 'Falha: '.($result['error'] ?? 'desconhecido'));
    }
}