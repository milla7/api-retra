<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Contacto extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if(isset( $this->data["archivo_contacto"] )){
            $nombre = $this->data->file('archivo_contacto')->getClientOriginalName();
            return (new MailMessage)
                        ->from('info@laretrateriaec.com', 'La Retrateria')
                        ->subject('Contacto Cliente: ' . $this->data['email_contacto'])
                        ->line('Please download the PDF.')
                        ->attach(public_path('archivos/' . $nombre))
                        ->markdown('panel.email.contacto', [
                            'data' => $this->data,
                        ]);
        }else{
            return (new MailMessage)
                        ->from('info@laretrateriaec.com', 'La Retrateria')
                        ->subject('Contacto Cliente: ' . $this->data['email_contacto'])

                        ->markdown('panel.email.contacto', [
                            'data' => $this->data,
                        ]);
        }
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
            //
        ];
    }
}
