<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Alert;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReadingAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public $alert;
    public $mail;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($alert, $mail = false)
    {
        $this->alert = $alert;
        $this->mail = $mail;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $now = time();
        return (
            $this->mail && 
            (Carbon::parse($this->alert->last_sent_email)->timestamp + 86400) < $now)    //desde que tenha passado mais de 24 horas desde o ultimo email deste alerta
            ? ['database', 'mail'] : ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $this->alert->last_sent_email = Carbon::now();
        $this->alert->save();
        return (new MailMessage)
                    ->subject('Alert Triggered! (' . $this->alert->name . ')')
                    ->greeting('Greetings> ' . $notifiable->name)
                    ->line('Your alert (' . $this->alert->name . ') has been triggered!')
                    ->action('Click here to see more details', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'alert_id' => $this->alert->id,
        ];
    }
}

