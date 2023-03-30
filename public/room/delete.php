<?php
require_once __DIR__ . "/../../bootstrap/bootstrap.php";

class RoomDeletePage extends CRUDPage
{

    protected function prepare(): void
    {
        parent::prepare();

        $roomId = filter_input(INPUT_POST, 'roomId', FILTER_VALIDATE_INT);
        if (!$roomId)
            throw new BadRequestException();

        //Should be impossible but just in case
        if($_SESSION['admin'] != 1)
            throw new AccessDeniedException();
        //když poslal data
        $employees = null;
        $stmt = PDOProvider::get()->prepare("SELECT employee_id FROM ".Staff::DB_TABLE." WHERE `room` = :roomId");
        $stmt->execute(["roomId" => $roomId]);
        $employees = $stmt->fetch();
        if($employees != null){
            $this->redirect(self::ACTION_DELETE, 0);
        }
        $success = Room::deleteByID($roomId);

        //přesměruj
        $this->redirect(self::ACTION_DELETE, $success);
    }

    protected function pageBody()
    {
        return "";
    }

}

$page = new RoomDeletePage();
$page->render();

?>
