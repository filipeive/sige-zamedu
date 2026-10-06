<?php

namespace App\Http\Controllers;

use App\Models\SmsInbox;
use App\Services\Sms\SmsManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmsInboxController extends Controller
{
    public function __construct(protected SmsManager $sms) {}

    public function index(Request $request)
    {
        return redirect()->route('sms.index', array_merge(['tab' => 'inbox'], $request->query()));
    }

    public function show($id)
    {
        $this->authorize('view_students');

        $message = SmsInbox::find($id);
        if (! $message) {
            return redirect()->route('sms.index', ['tab' => 'inbox'])
                ->with('warning', 'Mensagem não encontrada ou já eliminada.');
        }

        // Marcar como lida
        if (! $message->is_read) {
            $message->update([
                'is_read' => true,
                'read_at' => now(),
                'read_by' => Auth::id(),
            ]);
        }

        return view('sms.inbox_show', [
            'message' => $message->load(['student', 'parent', 'reader']),
            'unreadCount' => SmsInbox::where('is_read', false)->count(),
        ]);
    }

    public function markRead(SmsInbox $message)
    {
        $this->authorize('view_students');
        $message->update([
            'is_read' => true,
            'read_at' => now(),
            'read_by' => Auth::id(),
        ]);
        return back()->with('success', 'Mensagem marcada como lida.');
    }

    public function markAllRead()
    {
        $this->authorize('view_students');
        SmsInbox::where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
            'read_by' => Auth::id(),
        ]);
        return back()->with('success', 'Todas as mensagens marcadas como lidas.');
    }

    public function reply(Request $request, SmsInbox $message)
    {
        $this->authorize('view_students');

        $validated = $request->validate([
            'message' => 'required|string|max:480',
        ]);

        $result = app(\App\Services\Sms\SmsNotificationService::class)->send(
            phone: $message->from_phone,
            message: $validated['message'],
            channel: 'guardian',
            context: 'inbox-reply',
            student: $message->student,
            recipientName: $message->from_name,
        );

        if ($result['success'] ?? false) {
            return back()->with('success', 'Resposta enviada por SMS.');
        }

        return back()->with('error', 'Falha ao enviar: '.($result['error'] ?? 'desconhecido'));
    }
}