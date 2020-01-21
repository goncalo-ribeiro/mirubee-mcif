<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReadingThreePhase extends JsonResource
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'ip' => $this->ip,
            'time' => $this->time,
            'calc_time' => $this->calc_time,
            'v1' => $this->v1,
            'v2' => $this->v2,
            'v3' => $this->v3,
            'vt' => $this->vt,
            'i1' => $this->i1,
            'i2' => $this->i2,
            'i3' => $this->i3,
            'it' => $this->it,
            'p1' => $this->p1,
            'p2' => $this->p2,
            'p3' => $this->p3,
            'pt' => $this->pt,
            'a1' => $this->a1,
            'a2' => $this->a2,
            'a3' => $this->a3,
            'at' => $this->at,
            'r1' => $this->r1,
            'r2' => $this->r2,
            'r3' => $this->r3,
            'rt' => $this->rt,
            'q1' => $this->q1,
            'q2' => $this->q2,
            'q3' => $this->q3,
            'qt' => $this->qt,
            'f1' => $this->f1,
            'f2' => $this->f2,
            'f3' => $this->f3,
            'ft' => $this->ft,
            'e1' => $this->e1,
            'e2' => $this->e2,
            'e3' => $this->e3,
            'et' => $this->et,
            'o1' => $this->o1,
            'o2' => $this->o2,
            'o3' => $this->o3,
            'ot' => $this->ot,
            'ps' => $this->ps,
            'calc_day_week' => $this->calc_day_week,
            'calc_day_month' => $this->calc_day_month,
            'calc_year' => $this->calc_year,
            'calc_month' => $this->calc_month,
            'calc_hour' => $this->calc_hour,
            'calc_minute' => $this->calc_minute,
            'device' => $this->device->name,
        ];
    }
}
