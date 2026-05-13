<?php

namespace App\Enums;

enum UserRole: string
{
    case PATIENT = 'patient';

    case THERAPIST = 'therapist';

    case ADMIN_GLOBAL = 'admin_global';

    case ADMIN_CABANG = 'admin_cabang';
}