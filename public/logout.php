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
        header("Location: login.php" );
        return '';
    }

}

$page = new LogoutPage();
$page->render();

?>