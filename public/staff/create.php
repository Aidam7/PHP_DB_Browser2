<?php
require_once __DIR__ . "/../../bootstrap/bootstrap.php";

class EmployeeCreatePage extends CRUDPage
{
    private ?Staff $employee;
    private ?array $errors = [];
    private int $state;
    private array $allRooms = [];

    protected function prepare(): void
    {
        parent::prepare();
        $this->findState();
        $this->title = "Založit nového zaměstnance";

        //když chce formulář
        if ($this->state === self::STATE_FORM_REQUESTED)
        {
            //jdi dál
            $this->employee = new Staff();
        }

        //když poslal data
        elseif($this->state === self::STATE_DATA_SENT) {
            //načti je
            $this->employee = Staff::readPost();

            //zkontroluj je, jinak formulář
            $this->errors = [];
            $isOk = $this->employee->validate($this->errors);
            if (!$isOk)
            {
                $this->state = self::STATE_FORM_REQUESTED;
            }
            else
            {
                //ulož je
               $success = $this->employee->insert();

                //přesměruj
               $this->redirect(self::ACTION_INSERT, $success);
            }
        }
    }

    protected function pageBody()
    {
        $stmt = PDOProvider::get()->prepare("SELECT name, room_id FROM ". Room::DB_TABLE ." ORDER BY room_id;");
        $stmt->execute();
        $this->allRooms = $stmt->fetchAll();
        return MustacheProvider::get()->render(
            'employeeForm',
            [
                'formHeader' => 'Založit zaměstnance',
                'employee' => $this->employee,
                'rooms' => $this->allRooms,
                'errors' => $this->errors
            ]
        );
    }

    private function findState() : void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
            $this->state = self::STATE_DATA_SENT;
        else
            $this->state = self::STATE_FORM_REQUESTED;
    }

}

$page = new EmployeeCreatePage();
$page->render();

?>
