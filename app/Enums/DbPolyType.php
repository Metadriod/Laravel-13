<?php

namespace App\Enums;

enum DbPolyType: string
{
    /**
     * @Note
     * For Case USER: `user`, `user.userProfile`, `user.userProfile.address`  are logged as combined entities
     * for the \App\Services\HttpResources\AuditLogService -- similar to how there are stored, updated, and deleted in the
     * \App\Services\HttpResources\UserService class
     **/
    case USER = 'user';
    case API_KEY = 'api_key';
}
