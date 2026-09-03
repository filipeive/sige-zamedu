<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Communication;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display user notifications.
     */
    public function userNotifications()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notificação marcada como lida.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Todas as notificações foram marcadas como lidas.');
    }

    /**
     * Clear all notifications.
     */
    public function clearAll()
    {
        Auth::user()->notifications()->delete();

        return back()->with('success', 'Todas as notificações foram removidas.');
    }

    /**
     * List communications (announcements).
     */
    public function index()
    {
        if (!Auth::user()->hasRole(['admin', 'super_admin', 'secretary']) && !Auth::user()->can('view_communications')) {
            abort(403, 'Acesso não autorizado às comunicações.');
        }
        $communications = Communication::with('creator')->latest()->paginate(15);
        return view('communications.index', compact('communications'));
    }

    /**
     * Show form to create a communication.
     */
    public function create()
    {
        if (!Auth::user()->hasRole(['admin', 'super_admin', 'secretary']) && !Auth::user()->can('send_notifications')) {
            abort(403, 'Acesso não autorizado.');
        }
        return view('communications.create');
    }

    /**
     * Store and send a communication.
     */
    public function send(Request $request)
    {
        if (!Auth::user()->hasRole(['admin', 'super_admin', 'secretary']) && !Auth::user()->can('send_notifications')) {
            abort(403, 'Acesso não autorizado.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_audience' => 'required|in:all,teachers,parents,students',
            'priority' => 'required|in:low,medium,high',
            'is_public' => 'boolean',
        ]);

        $communication = Communication::create([
            'title' => $request->title,
            'message' => $request->message,
            'target_audience' => $request->target_audience,
            'priority' => $request->priority,
            'is_public' => $request->has('is_public'),
            'is_published' => true,
            'publish_at' => now(),
            'created_by' => Auth::id(),
        ]);

        // Enviar notificações para o público-alvo no sistema
        $this->notifyTargetAudience($communication);

        // Se a opção de envio por SMS estiver ativada, envia SMS aos encarregados
        if ($request->has('send_sms')) {
            try {
                $smsService = app(\App\Services\Sms\SmsNotificationService::class);
                $parents = \App\Models\ParentModel::whereNotNull('phone')->where('phone', '!=', '')->get();
                $schoolName = \App\Models\Setting::get('school_name', 'Escola');
                
                foreach ($parents as $parent) {
                    $smsMessage = "{$schoolName}: " . \Illuminate\Support\Str::limit($communication->title . " - " . $communication->message, 140);
                    $smsService->send(
                        phone: $parent->phone,
                        message: $smsMessage,
                        channel: 'guardian',
                        context: 'communication-sms',
                        recipientName: $parent->full_name
                    );
                }
            } catch (\Throwable $e) {
                // Log ou ignora falha de SMS secundária
            }
        }

        return redirect()->route('communications.index')
            ->with('success', 'Comunicado enviado com sucesso!');
    }

    /**
     * Notify target audience about the new communication.
     */
    private function notifyTargetAudience(Communication $communication)
    {
        $users = collect();

        if ($communication->target_audience === 'all') {
            $users = \App\Models\User::all();
        } elseif ($communication->target_audience === 'teachers') {
            $users = \App\Models\User::role('teacher')->get();
        } elseif ($communication->target_audience === 'parents') {
            $users = \App\Models\User::role('parent')->get();
        } elseif ($communication->target_audience === 'students') {
            $users = \App\Models\User::role('student')->get();
        }

        foreach ($users as $user) {
            $actionUrl = match ($user->role ?? '') {
                'parent' => route('parent.communications'),
                'teacher' => route('teacher.communications.index'),
                default => route('communications.index'),
            };

            $user->notify(new \App\Notifications\GenericNotification([
                'title' => 'Novo Comunicado: ' . $communication->title,
                'message' => $communication->excerpt,
                'action_url' => $actionUrl,
                'type' => 'communication'
            ]));
        }
    }
}
