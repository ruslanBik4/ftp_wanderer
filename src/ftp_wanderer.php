<?php
/*
* Напишете пакет для Composer, который будет заниматься тем, что с удаленного хоста загружать картинки и сохранять их на ФС.
*/

/**
*  Это определение класса,предоставляющего доступ к удаленному хосту ftp
*/
class ftp_Wanderer  {

    private $conn_id;
    private $is_login;
    
    protected $host;
    protected $port;
    protected $timeout;
    protected $login;
    protected $password;
    
    public function __construct($host, $port = 21, $timeout = 90)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }
    public function __destruct()
    {
        if( isset($this->conn_id) )
            ftp_close($this->conn_id);
    }
    //устанавливаем соединение
    protected function Opened()
    {
        $this->conn_id = ftp_connect($this->host, $this->port, $this->timeout);
        if ($this->conn_id === false)
            throw new Exception("Не удалось установить соединение с ".$this->host);
            
        return $this->conn_id;
    }
    // получаем соединение
    final public function GetConnection()
    {
        if( isset($this->conn_id) )
            return $this->conn_id;
        else
            return $this->Opened();
    }
    // работа с параметрами подключения
    public function Set_option($option, $value)
    {
        return ftp_set_option($this->GetConnection(), $option, $value);
    }
    public function Get_option($option)
    {
        return ftp_get_option($this->GetConnection(), $option);
    }
    // подключение к серверу
    protected function Login($login, $password)
    {
        
        if ( isset($this->is_login) )
           if ( ($this->login == $login) && ($this->password == $password) )
               return true;
        
        $this->login    = $login;
        $this->password = $password;
        
        
        return ( $this->is_login = ftp_login($this->GetConnection(), $this->login, $this->password) );
    }

}




