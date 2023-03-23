<?php
require_once __DIR__ . "/../../bootstrap/bootstrap.php";

class EmployeeDetailPage extends BasePage
{
    private $employee;
    private $employees;

    protected function prepare(): void
    {
        parent::prepare();
        //získat data z GET
        $employeeId = filter_input(INPUT_GET, 'employeeId', FILTER_VALIDATE_INT);
        if (!$employeeId)
            throw new BadRequestException();

        //najít místnost v databázi
        $this->employee = Staff::findByID($employeeId);
        if (!$this->employee)
            throw new NotFoundException();


        $stmt = PDOProvider::get()->prepare("SELECT e.`surname`, e.`name`, e.`employee_id`, e.`room`, e.`job`, r.`phone` FROM `employee` e, `room` r WHERE `employee_id`=:employeeId AND r.`room_id` = e.`room` ORDER BY `surname`, `name`");
        $stmt->execute(['employeeId' => $employeeId]);
        $this->employees = $stmt->fetchAll();

        $this->title = "Detail zaměstnance {$this->employee->employee_id}";

    }

    protected function pageBody()
    {
        //prezentovat data
        return MustacheProvider::get()->render(
            'employeeDetail',
            ['phone' => $this->employees->phone, 'employee' => $this->employees]
        );
    }

}

$page = new EmployeeDetailPage();
$page->render();

?>
