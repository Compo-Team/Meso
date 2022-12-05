<?php
namespace Meso;
require("log_handler.php");
class Base
{
    public $servername;
	public $username;
	public $password;
	public $dbname;
	public $connection;
	public  $settings;

	public function connect()
    {
        $connection = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
        if(mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: ".mysqli_connect_error();
            return false;
        }
        $this->connection = $connection;
        return true;
    }

    public function query($query, $optional_callback = '', $inputs = '')
    {
        cp:
        try
        {
            if(preg_match("/[;]/", $query) == 1)
            {
                $res_query = mysqli_multi_query($this->connection, $query);
            }
            else
            {
                $res_query = mysqli_query($this->connection, $query);
            }
        }
        catch(exception $e)
        {
            writeLog(str_replace("-", "", strval(date("Y-m-d")))."-".str_replace(":","", date("H:i:s"))."|"."agent"."|".mysqli_error($this->connection)."- this is an exception not gone"."%");
            return false;
        }
        $errno = mysqli_connect_errno();
        if(mysqli_error($this->connection) || $errno == 0)
        {
            if($this->settings['log_error'])
            {
                writeLog(str_replace("-", "", strval(date("Y-m-d")))."-".str_replace(":","", date("H:i:s"))."|"."agent"."|".mysqli_error($this->connection)."%");
            }
            if($this->settings['exception'])
            {
                return mysqli_error($this->connection);
            }
        }
        if($res_query == false || $errno == 2006 || $errno == 2013 || $errno == 0 || !mysqli_ping($this->connection))
        {
            $this->connect($this->servername, $this->username, $this->password, $this->dbname);
            goto cp;
        }
        if(mysqli_error($this->connection) == "asdf" && !empty($optional_callback))
        {
            if($inputs != '')
            {
                $optional_callback($inputs);
            }
            $optional_callback();
            goto cp;
        } //in progress
        return $res_query;
    }

}


?>