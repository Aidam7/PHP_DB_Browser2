<?php
require_once __DIR__ . "/../../bootstrap/bootstrap.php";

class StaffUpdatePage extends CRUDPage
{
    private Staff $employee;
    private $room;
    private ?array $errors = [];
    private array $allRooms = [];
    private array $mustacheArray = [];
    private int $state;

    protected function prepare(): void
    {
        parent::prepare();
        $this->findState();
        $this->title = "Upravit zaměstnance";

        //když chce formulář
        if ($this->state === self::STATE_FORM_REQUESTED) {
            $employeeId = filter_input(INPUT_GET, 'employeeId', FILTER_VALIDATE_INT);
            if (!$employeeId)
                throw new BadRequestException();

            //jdi dál
            $this->employee = Staff::findByID($employeeId);
            if (!$this->employee)
                throw new NotFoundException();
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
               $success = $this->employee->update();
                //přesměruj
               $this->redirect(self::ACTION_UPDATE, $success);
            }
        }
    }

    protected function pageBody()
    {

        $this->room = Room::findByID($this->employee->room);
        $stmt = PDOProvider::get()->prepare("SELECT name, room_id FROM room ORDER BY room_id;");
        $stmt->execute();
        $this->allRooms = $stmt->fetchAll();

        //Tohle je jediný způsob jak se mi podařilo donutit mustache printnout array bez původní místnost - je to stupidní, nevim proč se to děje, ale funguje to no... ¯\_(ツ)_/¯
        for ($i = 0; $i < count($this->allRooms);$i++){
            if($this->allRooms[$i]-> room_id !== $this->room->room_id){
                array_push($this->mustacheArray, $this->allRooms[$i]);
            }
        }
        return MustacheProvider::get()->render(
            'employeeForm',
            [
                'formHeader' => 'Upravit zaměstnance',
                'homeRoom' => $this->room,
                'employee' => $this->employee,
                'errors' => $this->errors,
                'rooms' => $this->mustacheArray
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

$page = new StaffUpdatePage();
    $page->render();

?>
