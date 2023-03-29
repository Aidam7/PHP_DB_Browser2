<?php
require_once __DIR__ . "/../../bootstrap/bootstrap.php";

class KeyDeletePage extends CRUDPage
{

    protected function prepare(): void
    {
        parent::prepare();

        $keyId = filter_input(INPUT_POST, 'keyId', FILTER_VALIDATE_INT);
        $employeeId = filter_input(INPUT_POST, 'employeeId', FILTER_VALIDATE_INT);
        if (!$keyId)
            throw new BadRequestException();
        if($_SESSION['admin'] != 1)
            throw new AccessDeniedException();

        //když poslal data
        $success = Key::deleteByID($keyId);

        //přesměruj
        $this->redirect(self::ACTION_DELETE, $success, "../staff/detail.php?employeeId=".$employeeId."&");
    }

    protected function pageBody()
    {
        return "";
    }

}

$page = new KeyDeletePage();
$page->render();

?>
