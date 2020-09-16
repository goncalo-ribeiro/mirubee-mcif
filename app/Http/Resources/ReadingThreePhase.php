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
            'v1' => decrypt($this->v1),
            'v2' => decrypt($this->v2),
            'v3' => decrypt($this->v3),
            'vt' => decrypt($this->vt),
            'i1' => decrypt($this->i1),
            'i2' => decrypt($this->i2),
            'i3' => decrypt($this->i3),
            'it' => decrypt($this->it),
            'p1' => decrypt($this->p1),
            'p2' => decrypt($this->p2),
            'p3' => decrypt($this->p3),
            'pt' => decrypt($this->pt),
            'a1' => decrypt($this->a1),
            'a2' => decrypt($this->a2),
            'a3' => decrypt($this->a3),
            'at' => decrypt($this->at),
            'r1' => decrypt($this->r1),
            'r2' => decrypt($this->r2),
            'r3' => decrypt($this->r3),
            'rt' => decrypt($this->rt),
            'q1' => decrypt($this->q1),
            'q2' => decrypt($this->q2),
            'q3' => decrypt($this->q3),
            'qt' => decrypt($this->qt),
            'f1' => decrypt($this->f1),
            'f2' => decrypt($this->f2),
            'f3' => decrypt($this->f3),
            'ft' => decrypt($this->ft),
            'e1' => decrypt($this->e1),
            'e2' => decrypt($this->e2),
            'e3' => decrypt($this->e3),
            'et' => decrypt($this->et),
            'o1' => decrypt($this->o1),
            'o2' => decrypt($this->o2),
            'o3' => decrypt($this->o3),
            'ot' => decrypt($this->ot),
            'ps' => decrypt($this->ps),
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
