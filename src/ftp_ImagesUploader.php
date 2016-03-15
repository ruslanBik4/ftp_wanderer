<?php
/**
* Class для перекачки картинок с удаленного сервера на свой ФС
*/
class ftp_ImagesUploader extends ftp_Wanderer {

    const img_types = '(jpg)|(png)|(gif)';
    
    private $path;
    private $mask;
    private $tmpfileName;
    private $num_image = 1;
    
    public function __construct($host, $port = 21, $timeout = 90)
    {
        parent::__construct($host, $port, $timeout);
    }
    public function __destruct()
    {
        if (isset($this->tmpfileName))
            unlink($this->tmpfileName);
            
        parent::__destruct();
    }
    // создаем временный файл
    private function CreateTmpFile()
    {
       if( ($this->tmpfileName = tempnam(sys_get_temp_dir(), "tmp")) === false)
           throw new Exception("Не удалось создать временный файл для приема данных."); 
           
       return $this->tmpfileName; 
    }
    // получаем имя временного файла
    protected function GetTmpFileName()
    {
        if (isset($this->tmpfileName))
            return $this->tmpfileName;
        else
            return $this->CreateTmpFile();
    }
    // скачиваем картинку по прямому URL
    private function GetImageFromURL($url)
    {
        file_put_contents($this->GetTmpFileName(), file_get_contents($url));
    }
    // скачиваем картинку по URL-адресу скрипта php
    private function GetImageFromPHP($url)
    {
        $ch = curl_init($url);
        $fp = fopen( $this->GetTmpFileName(), 'wb');
 
        $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';              
        $headers[] = 'Connection: Keep-Alive';         
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';         
        $user_agent = 'php';         
        
        $process = curl_init($url);         
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);         
        curl_setopt($process, CURLOPT_HEADER, 0);         
        curl_setopt($process, CURLOPT_USERAGENT, $useragent);         
        curl_setopt($process, CURLOPT_TIMEOUT, 30);         
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);         
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);         
        curl_setopt($process, CURLOPT_FILE, $fp);
        curl_exec($process);         
        curl_close($process);
        
        fclose($fp);
    }
    // скачиваем картинку по ftp-доступу
    private function GetImageFromFTP($match)
    {
    // Split FTP URI into: 
    // $match[0] = ftp://username:password@sld.domain.tld/path1/path2/filename.jpg 
    // $match[1] = ftp:// 
    // $match[2] = username 
    // $match[3] = password 
    // $match[4] = sld.domain.tld 
    // $match[5] = /path1/path2/ 
        $downloader = new ftp_ImageDownloader($match[4]);
        $downloader->Login($match[2], $match[3]);
        if ( !$downloader.SaveImageToFile( $this->GetTmpFileName(), $match[5] ) )
            throw new Exception("Ошибка при скачивании файла {$match[5]}");
        
    }
    /**
    *    перекачиваем картинку с удаленного хоста на свой ФС,
    *    в зависемости от переданной строки пути выбираем способ скачивания
    */
	public function MoveImage($login, $password, $path, $path_upload)
	{
        if (!$path_upload)
            throw new Exception("Не указан путь для сохранения на ФС");
            
        if ( !($this->login($login, $password)) )
            throw new Exception("Не удалось подключиться к {$this->host} с логином $login");
                    
        if (preg_match('/^ftp:\/\/(.*?):(.*?)@(.*?)(\/.*)/i', $path, $match)) {
        	$this->GetImageFromFTP($match);
        } elseif ( preg_match( '/^http.*\.php$/', $path) ) {
            $this->GetImageFromPHP($path);
        } elseif ( preg_match('/\.(' + $this::img_types + ')$/', $path) ) {
        	$this->GetImageFromURL($path);
        } else {
            throw new Exception("Непонятный формат данных.");	            
        }

        $this->num_image++;
            
        return ( ftp_put($this->GetConnection(), $path_upload + '/' + $this->num_image, $this->GetTmpFileName(), FTP_BINARY) );	
	}

}

