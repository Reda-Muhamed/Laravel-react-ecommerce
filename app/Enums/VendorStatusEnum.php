<?php

namespace App\Enums;

enum VendorStatusEnum:string
{

    case Active = 'Active';
    case Approved = 'Approved';
    case Pending = 'Pending';
    case Rejected = 'Rejected';
}
