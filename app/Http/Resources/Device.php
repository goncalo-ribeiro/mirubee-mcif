<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Device extends JsonResource
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
            'type' => $this->type,
            'mac_address' => $this->mac_address,
            'model' => $this->model,
            'soft' => $this->soft,
            'user' => $this->user->name,
            'site' => ['name' => is_null($this->site) ? null : $this->site->name,
                'id' => is_null($this->site) ? -1 : $this->site->id],
            'product_id' => $this->product_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
