<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeviceAssignedNotification extends Notification
{
    use Queueable;

    public $device;
    public $assignedBy;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($device, $assignedBy)
    {
        $this->device = $device;
        $this->assignedBy = $assignedBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $routeName = $notifiable->hasRole('admin') ? 'admin.devices.show' : 'operator.devices.show';
        
        return [
            'device_id' => $this->device->id,
            'device_name' => $this->device->name,
            'assigned_by' => $this->assignedBy->name,
            'message' => 'You have been assigned to a new device: ' . $this->device->name,
            'url' => route($routeName, $this->device->id)
        ];
    }
}
