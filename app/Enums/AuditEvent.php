<?php

namespace App\Enums;

enum AuditEvent: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case RETRIEVED = 'retrieved';
}
