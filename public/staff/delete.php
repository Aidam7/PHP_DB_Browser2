<?php
require_once __DIR__ . "/../../bootstrap/bootstrap.php";

class StaffDeletePage extends CRUDPage
{

    protected function prepare(): void
    {
        parent::prepare();

        $employeeId = filter_input(INPUT_POST, 'employeeId', FILTER_VALIDATE_INT);
        if (!$employeeId)
            throw new BadRequestException();

        //Should be impossible but just in case
        if($_SESSION['admin'] != 1)
            throw new AccessDeniedException();

        //když poslal data
        $success = Staff::deleteByID($employeeId);

        //přesměruj
        $this->redirect(self::ACTION_DELETE, $success);
    }

    protected function pageBody()
    {
        return "";
    }

}

$page = new StaffDeletePage();
$page->render();

?>
