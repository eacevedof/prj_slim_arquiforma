<?php

namespace App\Modules\Shared\Infrastructure\Enums;

final class MatchEnum
{
    public const CIF_NIF = "^[a-zA-Z0-9]{4,10}$";
    public const CIF_NIF_MESSAGE = "Solo se permiten letras y números, entre 4 y 10 caracteres";
    public const CIF_NIF_LENGTH = 10;

    public const USER_CODE = "^[A-Z0-9]{3,16}$";
    public const USER_CODE_MESSAGE = "Solo se permiten letras mayúsculas y números, entre 3 y 16 caracteres";
    public const USER_CODE_LENGTH = 16;

    public const PASSPORT = "^[a-zA-Z0-9]{4,16}$";
    public const PASSPORT_MESSAGE = "Solo se permiten letras y números, entre 4 y 16 caracteres";
    public const PASSPORT_LENGTH = 16;

    public const USER_NAME = "^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ ]{5,45}$";
    public const USER_NAME_MESSAGE = "Solo se permiten letras y espacios, entre 5 y 45 caracteres";
    public const USER_NAME_LENGTH = 45;

    public const POLICY_CODE = "^[a-zA-Z0-9\-\_\.]{4,16}$";
    public const POLICY_CODE_MESSAGE = "Solo se permiten letras, números, -, _ y ., entre 4 y 16 caracteres";
    public const POLICY_CODE_LENGTH = 16;

    public const USER_PASSWORD = "[a-zA-Z0-9 _*]{8,16}$";
    public const USER_PASSWORD_MESSAGE = "La contraseña debe tener entre 8 y 16 caracteres, y puede contener letras, números, espacios y _*";
    public const USER_PASSWORD_LENGTH = 16;

    public const PHONE = "[0-9]{9,15}$";
    public const PHONE_MESSAGE = "Solo se permiten números, entre 9 y 15 caracteres";
    public const PHONE_LENGTH = 15;

    public const MOBILE = "[0-9]{9,15}$";
    public const MOBILE_MESSAGE = "Solo se permiten números, entre 9 y 15 caracteres";
    public const MOBILE_LENGTH = 15;


    public const EMAIL = "(?=.{8,50}$)[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$";
    public const EMAIL_MESSAGE = "El email solo puede contener letras, números, puntos, guiones y guiones bajos, 
    y debe tener un formato válido. Debe estar entre 8 y 50 caracteres. Ej. usuario@ex.co";
    public const EMAIL_LENGTH = 50;


    public const COMPANY_NAME = "^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ _\-\.]{4,125}$";
    public const COMPANY_NAME_MESSAGE = "El nombre de la empresa solo puede contener letras, números, espacios, guiones, y puntos. Debe tener entre 4 y 125 caracteres";
    public const COMPANY_NAME_LENGTH = 125;


    public const FISCAL_ADDRESS = "^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ _\-\.\,]{4,125}$";
    public const FISCAL_ADDRESS_MESSAGE = "El domicilio fiscal solo puede contener letras, números, espacios, guiones, puntos y comas, y debe tener entre 4 y 125 caracteres";
    public const FISCAL_ADDRESS_LENGTH = 125;

    public const PERFIL_ALIAS = "[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ ]{2,48}";
    public const PERFIL_ALIAS_MESSAGE = "El alias solo puede contener letras, números y espacios, y debe tener entre 2 y 48 caracteres";
    public const PERFIL_ALIAS_LENGTH = 48;

    public const PERFIL_NAME = "[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ ]{2,48}";
    public const PERFIL_NAME_MESSAGE = "El nombre solo puede contener letras, números y espacios, y debe tener entre 2 y 48 caracteres";
    public const PERFIL_NAME_LENGTH = 48;

    public const SOCIAL_NUMBER = "^[0-9]{8,12}$";
    public const SOCIAL_NUMBER_MESSAGE = "El número de la seguridad social solo puede contener números, y debe tener entre 8 y 12 caracteres";
    public const SOCIAL_NUMBER_LENGTH = 12;


    public const PERFIL_TWITTER = "[a-zA-Z0-9_]{1,15}";
    public const PERFIL_TWITTER_MESSAGE = "El nombre de usuario de Twitter solo puede contener letras, números y guiones bajos, y debe tener entre 1 y 15 caracteres";
    public const PERFIL_TWITTER_LENGTH = 15;

    public const PERFIL_FACEBOOK = "^[a-zA-Z0-9._-]{5,50}$";
    public const PERFIL_FACEBOOK_MESSAGE = "El nombre de usuario de Facebook solo puede contener letras, números y puntos, y debe tener entre 5 y 50 caracteres";
    public const PERFIL_FACEBOOK_LENGTH = 50;

    public const PERFIL_INSTAGRAM = "^[a-zA-Z0-9._-]{1,30}$";
    public const PERFIL_INSTAGRAM_MESSAGE = "El nombre de usuario de Instagram solo puede contener letras, números, guiones bajos y puntos, y debe tener entre 1 y 30 caracteres";
    public const PERFIL_INSTAGRAM_LENGTH = 30;

    public const VEHICLE_NR = "^[a-zA-Z0-9]{4,10}$";
    public const VEHICLE_NR_MESSAGE = "El número de matrícula solo puede contener letras y números, y debe tener entre 4 y 10 caracteres";
    public const VEHICLE_NR_LENGTH = 10;

    public const PERFIL_WEB = "[a-z0-9._-]{3,41}+\.[a-z]{2,6}$";
    public const PERFIL_WEB_MESSAGE = "El nombre de usuario de la web solo puede contener letras, números, guiones bajos y puntos, y debe tener entre 3 y 41 caracteres";
    public const PERFIL_WEB_LENGTH = 41;

    public const PERFIL_EMAIL = "[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$";
    public const PERFIL_EMAIL_MESSAGE = "El email solo puede contener letras, números, puntos, guiones y guiones bajos, y debe tener un formato válido";
    public const PERFIL_EMAIL_LENGTH = 30;

    private function __construct() {}

}