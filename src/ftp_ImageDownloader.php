<?php
/**
* Class для скачивания картинок с ftp-сервера
*/
class ftp_ImageDownloader extends ftp_Wanderer {

	public function SaveImageToFile($tmpfileName, $remote_file)
	{
        return ftp_get($this->GetConnection(), $tmpfileName, $remote_file, FTP_BINARY);
	}

}