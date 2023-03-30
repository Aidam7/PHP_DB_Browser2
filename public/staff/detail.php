<?php
require_once __DIR__ . "/../../bootstrap/bootstrap.php";

class EmployeeDetailPage extends BasePage
{
    private $employee;
    private $employees;
    private array $keys = [];
    private $alert = [];

    protected function prepare(): void
    {
        parent::prepare();
        //získat data z GET
        $employeeId = filter_input(INPUT_GET, 'employeeId', FILTER_VALIDATE_INT);
        if (!$employeeId)
            throw new BadRequestException();

        //pokud přišel výsledek, zachytím ho
        $crudResult = filter_input(INPUT_GET, 'success', FILTER_VALIDATE_INT);
        $crudAction = filter_input(INPUT_GET, 'action');

        if (is_int($crudResult)) {
            $this->alert = [
                'alertClass' => $crudResult === 0 ? 'danger' : 'success'
            ];

            $message = '';
            if ($crudResult === 0)
            {
                $message = 'Operace nebyla úspěšná';
            }
            else if ($crudAction === CRUDPage::ACTION_DELETE)
            {
                $message = 'Smazání proběhlo úspěšně';
                $this->alert = [
                    'alertClass' => 'danger'
                ];
            }
            else if ($crudAction === CRUDPage::ACTION_INSERT)
            {
                $message = 'Klíč byl úspěšně založen';
            }

            $this->alert['message'] = $message;
        }

        //najít místnost v databázi
        $this->employee = Staff::findByID($employeeId);
        if (!$this->employee)
            throw new NotFoundException();


        $stmt = PDOProvider::get()->prepare("SELECT e.`surname`, e.`name`, e.`employee_id`, e.`room`, e.`job`,  e.`wage`, r.`phone`, r.`name` roomName, r.`room_id` FROM `employee` e, `room` r WHERE `employee_id`=:employeeId AND r.`room_id` = e.`room` ORDER BY `surname`, `name`");
        $stmt->execute(['employeeId' => $employeeId]);
        $this->employees = $stmt->fetchAll();

        $this->title = "Detail zaměstnance {$this->employee->employee_id}";

        $stmt = PDOProvider::get()->prepare("SELECT k.key_id, k.employee `employee_id`, k.room `room_id`, r.name `room_name` FROM `".Key::DB_TABLE."` k JOIN ".Room::DB_TABLE." r ON k.room = r.room_id WHERE k.employee =:employeeId ORDER BY r.name");
        $stmt->execute(['employeeId' => $employeeId]);
        $this->keys = $stmt->fetchAll();

    }

    protected function pageBody()
    {
        $html = "";
        if ($this->alert) {
            $html .= MustacheProvider::get()->render('crudResult', $this->alert);
        }
        $html .= MustacheProvider::get()->render(
            'employeeDetail',
            ['employee' => $this->employees]
        );
        $isAdmin = null;
        if($_SESSION['admin'] == 1)
            $isAdmin = true;
        $html .= MustacheProvider::get()->render(
            'keyList',['keys'=> $this->keys, 'employeeId' => $this->employee->employee_id, 'admin' => $isAdmin]
        );
        //prezentovat data
        return $html;
    }

}

$page = new EmployeeDetailPage();
$page->render();

?>
