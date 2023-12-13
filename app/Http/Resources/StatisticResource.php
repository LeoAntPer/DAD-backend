<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public static $format = "default";
    public function toArray(Request $request): array
    {
        switch (StatisticResource::$format) {
            case "admin":
                return [
                    "total_vcards" => $this->total_vcards,
                    "total_active_vcards" => $this->total_active_vcards,
                    "total_blocked_vcards" => $this->total_blocked_vcards,
                    "total_transactions" => $this->total_transactions,
                    "total_transactions_by_type" => $this->total_transactions_by_type,
                    "total_transactions_by_month" => $this->total_transactions_by_month,
                    "total_balance" => $this->total_balance,
                ];
            default:
                return [
                    "num_recived_transactions" => $this->num_recived_transactions,
                    "total_received" => $this->total_received,
                    "num_sent_transactions" => $this->num_sent_transactions,
                    "total_sent" => $this->total_sent,
                    "balance_by_month" => $this->balance_by_month,
                ];
        }
    }
}
