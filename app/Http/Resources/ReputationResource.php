<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReputationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'enterprise' => $this->enterprise,
            'number_of_blacklist' => $this->number_of_blacklist,
            'number_of_ban' => $this->number_of_ban,
            'prestige_score' => $this->prestige_score,
        ];
    }
}
