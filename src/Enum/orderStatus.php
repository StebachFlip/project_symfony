<?php

namespace App\Enum;

enum orderStatus: string
{
    case PENDING = 'pending';
    case SHIPPED = 'shipped';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    // Optionnel : une méthode pour obtenir un libellé pour chaque statut
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::SHIPPED => 'Shipped',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }
}
