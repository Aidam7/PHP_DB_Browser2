<?php
require_once __DIR__ . "/../../bootstrap/bootstrap.php";

class KeyCreatePage extends CRUDPage
{
    private ?Key $key;
    private ?array $errors = [];
    private int $state;
    private array $allRooms = [];

    protected function prepare(): void
    {
        parent::prepare();
        $this->findState();
        $this->title = "Založit nový klíč";

        //když chce formulář
        if ($this->state === self::STATE_FORM_REQUESTED)
        {
            //jdi dál
            $this->key = new Key();
            $this->key->employee_id = filter_input(INPUT_GET, 'employeeId', FILTER_VALIDATE_INT);
        }

        //když poslal data
        elseif($this->state === self::STATE_DATA_SENT) {
            //načti je
            $this->key = Key::readPost();

            //zkontroluj je, jinak formulář
            $this->errors = [];
            $isOk = $this->key->validate($this->errors);
            if (!$isOk)
            {
                $this->state = self::STATE_FORM_REQUESTED;
            }
            else
            {
                //ulož je
                $success = $this->key->insert();

                //přesměruj
                $this->redirect(self::ACTION_INSERT, $success);
            }
        }
    }

    protected function pageBody()
    {
        $stmt = PDOProvider::get()->prepare("SELECT name, room_id FROM ". Room::DB_TABLE ." ORDER BY name;");
        $stmt->execute();
        $this->allRooms = $stmt->fetchAll();
        return MustacheProvider::get()->render(
            'keyForm',
            [
                'formHeader' => 'Založit klíč',
                'key' => $this->key,
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

$page = new KeyCreatePage();
$page->render();

?>