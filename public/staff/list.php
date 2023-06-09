<?php
require_once __DIR__ . "/../../bootstrap/bootstrap.php";

class EmployeesPage extends CRUDPage
{
    private $alert = [];

    public function __construct()
    {
        $this->title = "Výpis zaměstnanců";
    }

    protected function prepare(): void
    {
        parent::prepare();
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
            else if ($crudAction === self::ACTION_DELETE)
            {
                $message = 'Smazání proběhlo úspěšně';
                $this->alert = [
                    'alertClass' => 'danger'
                ];
            }
            else if ($crudAction === self::ACTION_INSERT)
            {
                $message = 'Zaměstnanec byl založen úspěšně';
            }
            else if ($crudAction === self::ACTION_UPDATE)
            {
                $message = 'Úprava zaměstnance byla úspěšná';
            }

            $this->alert['message'] = $message;
        }

    }


    protected function pageBody()
    {
        $html = "";
        //zobrazit alert
        if ($this->alert) {
            $html .= MustacheProvider::get()->render('crudResult', $this->alert);
        }

        //získat data
//        $employees = Staff::getAll(['name' => 'ASC']);
        $stmt = PDOProvider::get()->prepare("SELECT e.`surname`, e.`name`, e.`employee_id`, e.`room`, e.`job`, r.`phone`, r.`name` roomName, r.`room_id` FROM `employee` e, `room` r WHERE e.`room` = r.`room_id` ORDER BY e.`surname`, e.`name`");
        $stmt->execute();
        $employees = $stmt->fetchAll();
        //prezentovat data
        $isAdmin = null;
        if($_SESSION['admin'] == 1)
            $isAdmin=true;
        $html .= MustacheProvider::get()->render('employeeList',['employees' => $employees, 'admin' => $isAdmin, 'ownId' => $_SESSION['user']]);

        return $html;
    }

}

$page = new EmployeesPage();
$page->render();

?>
