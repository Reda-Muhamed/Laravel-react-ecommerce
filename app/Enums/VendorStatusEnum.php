<?php

namespace App\Enums;

enum VendorStatusEnum:string
{

    case Active = 'Active';
    case Approved = 'Approved';
    case Pending = 'Pending';
    case Rejected = 'Rejected';
   public static function labels(): array {
        return [
            self::Active->value => __('Active'),
            self::Approved->value => __('Approved'),
            self::Pending->value => __('Pending'),
            self::Rejected->value => __('Rejected'),
        ];
    }
    public  function label():string {
        return match($this) {
            self::Active => __('Active'),
            self::Approved => __('Approved'),
            self::Pending => __('Pending'),
            self::Rejected => __('Rejected'),
        };
    }
    public static function colors(): array {
        return [
            'gray'=>self::Pending->value,
            'success'=>self::Approved->value,
            'danger'=>self::Rejected->value,
        ];
    }
}
