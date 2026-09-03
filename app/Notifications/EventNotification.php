<?php

namespace App\Notifications;

use App\Models\Event;
use App\Notifications\Channels\SmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventNotification extends Notification
{
    use Queueable;

    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (!empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        if (config('services.sms.enabled') && method_exists($notifiable, 'routeNotificationForSms')) {
            $channels[] = 'sms';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Novo Evento: ' . $this->event->title)
            ->greeting('Olá!')
            ->line('Um novo evento foi agendado na escola.')
            ->line('**Evento:** ' . $this->event->title)
            ->line('**Data:** ' . $this->event->event_date->format('d/m/Y'))
            ->line('**Horário:** ' . $this->event->start_time->format('H:i') . ' - ' . $this->event->end_time->format('H:i'))
            ->action('Ver Detalhes', route('events.show', $this->event))
            ->line('Aguardamos sua participação!');
    }

    public function toSms(object $notifiable): SmsMessage
    {
        $content = "📅 {$this->event->title}\n{$this->event->event_date->format('d/m/Y')} · {$this->event->start_time->format('H:i')}\n";
        $content .= config('app.name');

        return new SmsMessage($content);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'title' => $this->event->title,
            'event_date' => $this->event->event_date->format('d/m/Y'),
            'type' => $this->event->type,
            'message' => 'Novo evento agendado: ' . $this->event->title,
        ];
    }
}
