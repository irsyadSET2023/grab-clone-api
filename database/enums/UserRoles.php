<?php

namespace Database\Enums;

use BenSampo\Enum\Enum;

class UserRoles extends Enum
{
    const SUPER_ADMIN = "Super Admin";
    const RESTAURANT_MANAGER = "Restaurant Manager";
    const CUSTOMER = "Customer";
}
