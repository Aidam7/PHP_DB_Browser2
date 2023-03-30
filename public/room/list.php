<?php
require_once __DIR__ . "/../../bootstrap/bootstrap.php";

class RoomsPage extends CRUDPage
{
    private $alert = [];

    public function __construct()
    {
        $this->title = "Výpis místností";
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
            if ($crudResult === 0 && $crudAction === self::ACTION_DELETE){
                $message = "Místnost nejde smazat, je možné, že v ní sídlí zaměstnanci";
                $this->alert = [
                    "alertClass" => 'warning'
                ];
            }
            else if ($crudResult === 0)
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
                $message = 'Místnost založena úspěšně';
            }
            else if ($crudAction === self::ACTION_UPDATE)
            {
                $message = 'Úprava místnosti byla úspěšná';
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
        $rooms = Room::getAll(['name' => 'ASC']);
        //prezentovat data
        $isAdmin = null;
        if($_SESSION['admin'] == 1)
            $isAdmin=true;
        $html .= MustacheProvider::get()->render('roomList',['rooms' => $rooms, "admin" => $isAdmin]);

        return $html;
    }

}

$page = new RoomsPage();
$page->render();

?>
