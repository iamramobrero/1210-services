<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'status_badge' => $this->status_badge,
            'content' => $this->content,
            'routes' => [
                'edit' => route('tasks.edit',[$this->id]),
                'destroy' => route('tasks.destroy',[$this->id]),
            ]
            ];
    }
}
