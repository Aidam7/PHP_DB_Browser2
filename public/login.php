<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class LoginPage extends BasePage
{
    private ?string $login = null;
    private ?string $password = null;
    public function __construct()
    {
        $this->title = "Přihlašte se prosím";
    }
    protected function prepare(): void
    {
        parent::prepare();
        $this->login = filter_input(INPUT_POST,'login');
        $this->password = filter_input(INPUT_POST,'password');
        if($this->login !== null && $this->password !== null){
            header("Location: index.php");
        }
    }

    protected function pageBody()
    {
        $html =  MustacheProvider::get()->render('loginForm');
        return $html;
    }

}

$page = new LoginPage();
$page->render();

?>
