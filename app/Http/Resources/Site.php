<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Tariff as TariffResource;

class Site extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user' => $this->user->name,
            'location' => is_null($this->location) ? null : decrypt($this->location),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'tariff' => is_null($this->tariff) ? null : new TariffResource($this->tariff),
        ];
    }
}
