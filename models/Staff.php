<?php

//namespace models;

class Staff
{
    public const DB_TABLE = "employee";

    public ?int $employee_id;
    public ?string $name;
    public ?string $surname;
    public ?string $job;
    public ?int $wage;
    public ?int $room;
    public ?string $login;
    public ?string $password;
    public ?bool $admin;

    public function  __construct(?int $employee_id = null, ?string $name = null, ?string $surname = null, ?string $job = null, ?int $wage =  null, ?int $room = null, ?string $login = null, ?string $password = null, ?bool $admin = null){
        $this->employee_id = $employee_id;
        $this->name = $name;
        $this->surname = $surname;
        $this->job = $job;
        $this->wage = $wage;
        $this ->room = $room;
        $this->login = $login;
        $this->password = $password;
        $this->admin = $admin;
    }

    public static function findByID(int $id) : ?self
    {
        $pdo = PDOProvider::get();
       $stmt = $pdo->prepare("SELECT * FROM `".self::DB_TABLE."` WHERE `employee_id`= :employeeId");
        /* $stmt = $pdo->prepare("SELECT e.`surname`, e.`name`, e.`employee_id`, e.`room`, e.`job`, r.`phone` FROM `employee` e, `room` r WHERE `employee_id`=:employeeId AND r.`room_id` = e.`room` ORDER BY `surname`, `name`");*/
        $stmt->execute(['employeeId' => $id]);

        if ($stmt->rowCount() < 1)
            return null;

        $employee = new self();
        $employee->hydrate($stmt->fetch());
        return $employee;
    }

    /**
     * @return Room[]
     */
    public static function getAll($sorting = []) : array
    {
        $sortSQL = "";
        if (count($sorting))
        {
            $SQLchunks = [];
            foreach ($sorting as $field => $direction)
                $SQLchunks[] = "`{$field}` {$direction}";

            $sortSQL = " ORDER BY " . implode(', ', $SQLchunks);
        }

        $pdo = PDOProvider::get();
        $stmt = $pdo->prepare("SELECT * FROM `".self::DB_TABLE."`" . $sortSQL);
        $stmt->execute([]);

        $staff = [];
        while ($staffData = $stmt->fetch())
        {
            $employee = new Staff();
            $employee->hydrate($staffData);
            $staff[] = $employee;
        }

        return $staff;
    }

    private function hydrate(array|object $data)
    {
        $fields = ['employee_id', 'name', 'surname', 'job', 'wage', 'room', 'login', 'password', 'admin'];
        if (is_array($data))
        {
            foreach ($fields as $field)
            {
                if (array_key_exists($field, $data))
                    $this->{$field} = $data[$field];
            }
        }
        else
        {
            foreach ($fields as $field)
            {
                if (property_exists($data, $field))
                    $this->{$field} = $data->{$field};
            }
        }
    }

    public function insert() : bool
    {
        $query = "INSERT INTO ".self::DB_TABLE." (`name`, `surname`, `phone`) VALUES (:name, :surname, :phone)";
        $stmt = PDOProvider::get()->prepare($query);
        $result = $stmt->execute(['name'=>$this->name, 'no'=>$this->no, 'phone'=>$this->phone]);
        if (!$result)
            return false;

        $this->employee_id = PDOProvider::get()->lastInsertId();
        return true;
    }

    public function update() : bool
    {
        if (!isset($this->employee_id) || !$this->employee_id)
            throw new Exception("Cannot update model without ID");

        $query = "UPDATE ".self::DB_TABLE." SET `name` = :name, `no` = :no, `phone` = :phone WHERE `room_id` = :roomId";
        $stmt = PDOProvider::get()->prepare($query);
        return $stmt->execute(['roomId'=>$this->employee_id, 'name'=>$this->name, 'no'=>$this->no, 'phone'=>$this->phone]);
    }

    public function delete() : bool
    {
        return self::deleteByID($this->employee_id);
    }

    public static function deleteByID(int $employeeId) : bool
    {
        $query = "DELETE FROM `".self::DB_TABLE."` WHERE `employee_id` = :Id";
        $stmt = PDOProvider::get()->prepare($query);
        return $stmt->execute(['Id'=>$employeeId]);
    }

    public function validate(&$errors = []) : bool
    {
        if (!isset($this->name) || (!$this->name))
            $errors['name'] = 'Jméno nesmí být prázdné';

        if (!isset($this->no) || (!$this->no))
            $errors['no'] = 'Číslo musí být vyplněno';

        return count($errors) === 0;
    }

    public static function readPost() : self
    {
        $employee = new Staff();
        $employee->room_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

        $employee->name = filter_input(INPUT_POST, 'name');
        if ($employee->name)
            $employee->name = trim($employee->name);

        $employee->no = filter_input(INPUT_POST, 'no');
        if ($employee->no)
            $employee->no = trim($employee->no);

        $employee->phone = filter_input(INPUT_POST, 'phone');
        if ($employee->phone)
            $employee->phone = trim($employee->phone);
        if (!$employee->phone)
            $employee->null;

        return $employee;
    }
}

