<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Notifications\Channels\SmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MonthlyFeeGeneratedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Payment $payment)
    {
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
        $studentName = $this->payment->student?->full_name ?? 'seu educando';
        $dueDate = $this->payment->due_date?->format('d/m/Y');
        $amount = number_format((float) $this->payment->total_amount, 2, ',', '.');

        return (new MailMessage)
            ->subject('Nova propina gerada')
            ->greeting('Olá!')
            ->line("Foi gerada uma nova propina para {$studentName}.")
            ->line("Referência: {$this->payment->reference_number}")
            ->line("Valor: {$amount} MT")
            ->line("Prazo de pagamento: {$dueDate}")
            ->action('Ver pagamentos', route('parent.payments'));
    }

    public function toSms(object $notifiable): SmsMessage
    {
        $studentName = $this->payment->student?->full_name ?? 'seu educando';
        $amount = number_format((float) $this->payment->total_amount, 2, ',', '.');
        $dueDate = $this->payment->due_date?->format('d/m/Y');

        $content = "Nova propina {$studentName}\nRef: {$this->payment->reference_number}\n";
        $content .= "Valor: {$amount} MT · Vence: {$dueDate}";

        return new SmsMessage($content);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nova propina gerada',
            'message' => "Referência {$this->payment->reference_number} disponível até " . $this->payment->due_date?->format('d/m/Y'),
            'action_url' => route('parent.payments'),
            'type' => 'payment',
            'payment_id' => $this->payment->id,
            'student_id' => $this->payment->student_id,
            'reference' => $this->payment->reference_number,
            'amount' => (float) $this->payment->total_amount,
            'due_date' => $this->payment->due_date?->format('Y-m-d'),
        ];
    }
}
