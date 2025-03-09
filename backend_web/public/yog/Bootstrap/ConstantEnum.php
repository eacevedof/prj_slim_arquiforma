<?php
namespace Yog\Bootstrap;

abstract class ConstantEnum
{
    public const DEBUG = 1;
    public const COMMENT_OFF = 0;
    public const COMMENT_HASH = 1;
    public const COMMENT_DASH = 2;
    public const COMMENT_START = 0;

    public const XML_NOSTATE = 0;
    public const XML_HOST = 1;
    public const XML_USER = 2;
    public const XML_DB = 3;
    public const XML_PWD = 4;
    public const XML_PORT = 5;
    public const XML_QUERY = 6;
    public const XML_CHARSET = 7;
    public const XML_LIBXML2_TEST_QUERY = 8;

    /* You will need to change the version in processquery method too, where it shows: $versionheader = 'TunnelVersion:5.13.1' */
    public const TUNNEL_VERSION_13_21 = "13.21";
    public const TUNNEL_VERSION_STRING = "TunnelVersion:";
    public const PHP_VERSION_ERROR = "PHP_VERSION_ERROR";
    public const PHP_MODULE_ERROR = "PHP_MODULE_NOT_INSTALLED";
}