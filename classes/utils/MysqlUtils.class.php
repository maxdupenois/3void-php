<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mysqlutilsclass
 *
 * @author Max
 */
class MysqlUtils {
    //put your code here

    public static function unixToMysql($timestamp)
    {
        
        return date('Y-m-d H:i:s', $timestamp);
    }
    public static function mysqlToUnix($timestamp)
    {

        return strtotime($timestamp);
    }
}

?>
