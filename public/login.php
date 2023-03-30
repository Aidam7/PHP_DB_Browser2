<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class LoginPage extends BasePage
{
    protected Staff $user;
    private ?string $login = null;
    private ?string $password = null;
    private array $errors = [];
    public function __construct()
    {
        $this->title = "Přihlašte se prosím";

    }

    public function render(): void
    {
        if (!isset($_SESSION))
            session_start();

        $this->prepare();

        if (isset($_SESSION['user'])) {
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
        $this->login = filter_input(INPUT_POST, 'login');
        $this->password = filter_input(INPUT_POST, 'password');
        if ($this->login == null ||  $this->password == null){
//           $this->errors[0] = true;
            return;
        }
        if ($this->login == "" && $this->password != ""){
            $this->errors[0] = true;
            $this->pageBody();
            return;
        }
        if($this->login != "" && $this->password == ""){
            $this->errors[0] = true;
            $this->pageBody();
            return;
        }
        $stmt = PDOProvider::get()->prepare("SELECT `password` FROM " . Staff::DB_TABLE . " WHERE `login` =:login");
        $stmt->execute(["login" => $this->login]);
        $userPassword = $stmt->fetch();
        if (!password_verify($this->password, $userPassword->password)){
            $this->errors[0] = true;
            $this->pageBody();
            return;
        }
        $stmt = PDOProvider::get()->prepare("SELECT `employee_id`, `name`, `surname`, `admin` FROM " . Staff::DB_TABLE . " WHERE `login` =:login");
        $stmt->execute(["login" => $this->login]);
        $user = $stmt->fetch();
        session_abort();
        session_start();
        $_SESSION['user'] = $user->employee_id;
        $_SESSION['name'] = $user->name;
        $_SESSION['surname'] = $user->surname;
        $_SESSION['admin'] = $user->admin;
    }

    protected function pageBody()
    {
        $html = MustacheProvider::get()->render('loginForm', ["login" => $this->login, "errors" => $this->errors]);
        return $html;
    }
}

$page = new LoginPage();
$page->render();

?>
