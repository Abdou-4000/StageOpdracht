<?php
namespace App\Events;

use App\Models\ChatMessage;
use App\Models\User; // Voeg User model toe
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // Belangrijk!
// use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // Gebruik dit als je GEEN queues wilt gebruiken (niet aanbevolen voor productie)
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast // Implementeer ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user; // Maak de data public zodat het meegestuurd wordt
    public ChatMessage $message;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, ChatMessage $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Definieer het Pusher channel waarop dit event wordt uitgezonden.
        // 'public-chat' is een voorbeeldnaam voor een publiek kanaal.
        return [
            new Channel('public-chat'),
            // Voor privé chats zou je PrivateChannel of PresenceChannel gebruiken
            // new PrivateChannel('chat.'.$this->message->receiver_id),
        ];
    }

    /**
     * De naam waaronder het event gebroadcast wordt.
     * Standaard is dit de class naam (MessageSent). Je kunt dit aanpassen.
     * In Vue luister je dan naar '.App\\Events\\MessageSent' (let op de namespace en de . ervoor)
     * of je geeft een custom naam:
     */
    // public function broadcastAs(): string
    // {
    //     return 'new-message'; // In Vue luister je dan naar '.new-message'
    // }

    /**
     * De data die gebroadcast wordt. Standaard alle public properties.
     * Je kunt dit specifiek maken:
     */
    public function broadcastWith(): array
    {
        Log::info('Broadcasting message', [
            'message_id' => $this->message->id,
            'user_id' => $this->user->id
        ]);
        
        return [
            'message' => [
                'id' => $this->message->id,
                'message' => $this->message->message,
                'created_at' => $this->message->created_at,
                'user' => $this->user
            ]
        ];
    }
}