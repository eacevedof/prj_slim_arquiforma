<?php

namespace App\Modules\Shared\Infrastructure\Enums;

enum RouteEnum: string
{
    case HOME = "/";
    case CONTACT = "/contact/send-message";
    case USERS = "/users";
    case USER_ID = "/users/{id}";

}
