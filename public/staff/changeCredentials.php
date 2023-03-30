<?php
require_once __DIR__ . "/../../bootstrap/bootstrap.php";

class CredentialsChangePage extends CRUDPage
{
    protected Staff $user;
    protected function prepare(): void
    {
        parent::prepare();

        $employeeId = $_SESSION['user'];
        $newPassword = filter_input(INPUT_POST, 'password');
        $oldPassword = filter_input(INPUT_POST, 'oldPassword');
        $login = filter_input(INPUT_POST, 'login');
        if (!$employeeId)
            throw new BadRequestException();

        //Should be impossible but just in case
        if($_SESSION['user'] != $employeeId)
            throw new AccessDeniedException();
        $this->user = Staff::findByID($_SESSION['user']);
        if(/*password_verify(*/$oldPassword/*,PASSWORD_DEFAULT)*/ == $this->user->password && $this->user->login){
            $query = $stmt = PDOProvider::get()->prepare("UPDATE ".Staff::DB_TABLE." SET `login` = :login, `password` = :password WHERE `employee_id` = :employeeId");
            $stmt = PDOProvider::get()->prepare($query);
            $stmt->execute([
                'login'=>$login,
                'password'=>password_hash($newPassword,PASSWORD_DEFAULT, array('cost' => 15)),
                'employee_id'=>$employeeId
                ]);
        }



        //přesměruj
//        $this->redirect(self::ACTION_DELETE, $success);
    }

    protected function pageBody()
    {
        $html =  MustacheProvider::get()->render('changeLoginForm',["login"=>$this->user->login]);
        return $html;
    }

}

$page = new CredentialsChangePage();
$page->render();

?>
