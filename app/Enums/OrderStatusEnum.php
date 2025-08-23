<?php

namespace App\Enums;

enum OrderStatusEnum:string
{
    case Draft = "Draft";
    case Paid = "Paid";
    case Shipped="Shipped";
    case Delivered= "Delivered";
    case Cancelled= "Cancelled";
    public static function labels(){
        return [
            self::Draft=>__("Draft"),
            self::Paid=>__("Paid"),
            self::Shipped=> __("Shipped"),
            self::Delivered=> __("Delivered"),
            self::Cancelled=>__("Cancelled")
            ];
    }

}
