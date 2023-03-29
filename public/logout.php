<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class LogoutPage extends BasePage
{
    public function __construct()
    {
        $this->title = "Odhlášení";
    }

    protected function pageBody() :string
    {
        $_SESSION = array();
        header( "refresh:3;url=login.php" );
        return '<h2>Odhlášení proběhlo úspěšně</h2>';
    }

}

$page = new LogoutPage();
$page->render();

?>