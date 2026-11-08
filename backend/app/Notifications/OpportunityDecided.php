<?php

namespace App\Notifications;

use App\Models\Opportunity;
use Illuminate\Notifications\Notification;

final class OpportunityDecided extends Notification
{
    public const KINDS = ['opportunity_approved', 'opportunity_rejected'];

    public function __construct(private readonly string $kind, private readonly Opportunity $opportunity) {}

    public static function for(Opportunity $opportunity, bool $approved): self
    {
        return new self($approved ? 'opportunity_approved' : 'opportunity_rejected', $opportunity);
    }

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array{kind: string, opportunity_id: int, code: string, title: string} */
    public function toArray(object $notifiable): array
    {
        return [
            'kind' => $this->kind,
            'opportunity_id' => $this->opportunity->id,
            'code' => $this->opportunity->code,
            'title' => $this->opportunity->title,
        ];
    }
}
