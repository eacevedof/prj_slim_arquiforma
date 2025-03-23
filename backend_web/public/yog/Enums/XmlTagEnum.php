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
    case E_I = "e_i";
    case NUM_ROW_RESULT = "a_r";

    case FIELD_NAME = "n";
    case FIELD_TABLE = "t";
    case FIELD_MAX_LENGTH = "m";
    case FIELD_TYPE = "ty";
    case FIELD_D = "d";

    case ROW_INFO = "r_i";
    case ROW = "r";

}