<?php

namespace Yog\Enums;

enum XmlTagEnum: string
{
    case CONNECT_INFO = "connect_info";
    case HOST = "host";
    case USER = "user";
    case PASSWORD = "password";
    case DB = "db";
    case CHARSET = "charset";
    case PORT = "port";
    case QUERY = "query";

    case QUERYLEN = "querylen";
    case QUERY_INFO = "query_info";


    case COLUMN = "c";
    case FIELD_INFORMATION = "f_i";
    case SERVER_INFO = "s_v";
    case M_I = "m_i";
    case NUM_ROW_RESULT = "a_r";

}