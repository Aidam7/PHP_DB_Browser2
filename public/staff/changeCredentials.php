<?php
require_once __DIR__ . "/../../bootstrap/bootstrap.php";

class CredentialsChangePage extends CRUDPage
{
    protected Staff $user;
    private int $employeeId;

    protected function prepare(): void
    {
        parent::prepare();

        $this->employeeId = filter_input(INPUT_POST, 'employeeId');
        $newPassword = filter_input(INPUT_POST, 'password');

        $oldPassword = filter_input(INPUT_POST, 'oldPassword');
        $login = filter_input(INPUT_POST, 'login');

        //Should be impossible but just in case
        if ($_SESSION['user'] != $this->employeeId)
            throw new AccessDeniedException();
        if ($newPassword != null)
            $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        if ($oldPassword == null)
            $oldPassword = "";
        $this->user = Staff::findByID($_SESSION['user']);
        if ($this->user->login != $login)
            return;

        if (password_verify($oldPassword, $this->user->password)){
            $query = "UPDATE " . Staff::DB_TABLE . " SET `login` = :login, `password` = :password WHERE `employee_id` = :employeeId";
            $stmt = PDOProvider::get()->prepare($query);
        }
        if ($stmt->execute([ 'login' => $login, 'password' => $newPassword, 'employeeId' => $this->employeeId])){
            $this->redirect(self::ACTION_UPDATE, true);
        }
    }

    protected function pageBody()
    {
        $html = MustacheProvider::get()->render('changeLoginForm', ["login" => $this->user->login, "employeeId" => $this->employeeId]);
        return $html;
    }

}

$page = new CredentialsChangePage();
$page->render();

?>
