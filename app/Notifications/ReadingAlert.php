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
    public $reading;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($alert, $mail = false, $reading)
    {
        $this->alert = $alert;
        $this->mail = $mail;
        $this->reading = $reading;
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
        $last = $this->alert->last_sent_email;
        $anEmailWasSentInTheLast24Hours = false;
        if($last === null || Carbon::parse($last)->timestamp + 86400 < $now){
            $anEmailWasSentInTheLast24Hours = true;
        }
        Log::debug('now ' . $now);
        Log::debug('24h? ' . $anEmailWasSentInTheLast24Hours);
        return ($this->mail && $anEmailWasSentInTheLast24Hours) ? ['database', 'mail'] : ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        Log::debug('sending mail');
        $this->alert->last_sent_email = Carbon::now();
        $this->alert->save();
        return (new MailMessage)
                    ->subject('Alert Triggered! (' . $this->alert->name . ')')
                    ->greeting('Greetings ' . $notifiable->name)
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
            'reading' => $this->reading,
        ];
    }
}

