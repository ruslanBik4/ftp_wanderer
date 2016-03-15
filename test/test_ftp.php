<?php
// require_once 'PHPUnit/Framework.php';
require_once __DIR__ . '/../autoloader.php';


/**
 * Class предназначен для тестирования класса ftp_ImagesUploader
 */
class ftp_ImagesUploaderTest extends PHPUnit_Framework_TestCase {
    
    protected $ftp_iup;

    protected function setUp()
    {
        $this->ftp_iup = new ftp_ImagesUploader( 'tfsoft.org.ua' ); 
    }

    protected function tearDown()
    {
        $this->ftp_iup = NULL;
    }
    /** 
    * @dataProvider providerMoveImage 
    */
    public function testMoveImage($login, $password, $path, $path_upload)
    {
        
        $this->assertTrue($this->ftp_iup->MoveImage($login, $password, $path, $path_upload)); 
        
     }
     public function providerMoveImage()
     {
         return array (
            array ('anonymous', 'anonymous', 'http://php.net/images/logo.php', 'pub'), 
            array ('anonymous', '', 'http://php.net/images/logo.php', 'pub'), 
            array ('anonymous', '', 'http://php.net/images/logo.php', 'pub')
        ); 
     }
}


