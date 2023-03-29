<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class LoginPage extends BasePage
{
    protected Staff $user;
    private ?string $login = null;
    private ?string $password = null;
    public function __construct()
    {
        $this->title = "Přihlašte se prosím";

    }
    public function render(): void
    {
        if(!isset($_SESSION))
            session_start();

        $this->prepare();

        if(isset($_SESSION['user'])){
            header("Location: /index.php");
        }
        $this->sendHttpHeaders();

        $m = MustacheProvider::get();
        $data = [
            'lang' => AppConfig::get('app.lang'),
            'title' => $this->title,
            'pageHeader' => $this->pageHeader(),
            'pageBody' => $this->pageBody(),
            'pageFooter' => $this->pageFooter()
        ];

        echo $m->render("page", $data);
    }
    protected function prepare(): void
    {
        parent::prepare();
        $this->login = filter_input(INPUT_POST,'login');
        $this->password = filter_input(INPUT_POST,'password');
        if($this->login !== null || $this->login !== "" && $this->password !== null || $this->password !== ""){
            $stmt = PDOProvider::get()->prepare("SELECT * FROM ".Staff::DB_TABLE." WHERE `login` =:login AND `password` =:password");
            $stmt ->execute(["login" => $this->login, "password" => $this->password]);
            $user = $stmt->fetch();
            session_abort();
            session_start();
            $_SESSION['user'] = $user->employee_id;
            $_SESSION['admin'] = $user->admin;
        }
    }

    protected function pageBody()
    {
        $html =  MustacheProvider::get()->render('loginForm',["login"=>$this->login]);
        return $html;
    }

}

$page = new LoginPage();
$page->render();

?>
