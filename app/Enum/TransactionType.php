<?php

namespace App\Enum;

enum TransactionType
{
    case BUY = 'buy';
    case SELL = 'sell';
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';



    public static function getTransactionType(): array
    {
        return [
            self::BUY,
            self::SELL,
            self::DEPOSIT,
            self::WITHDRAW
        ];
    }

    public static function valueOf($value)
    {
        return match ($value) {
            self::BUY => self::BUY,
            self::SELL => self::SELL,
            self::DEPOSIT => self::DEPOSIT,
            self::WITHDRAW => self::WITHDRAW,
            default => throw new \InvalidArgumentException("Invalid Transaction Type")
        };
    }

    public static function checkType($value): bool
    {
        return in_array($value, self::getTransactionType());
    }

}
