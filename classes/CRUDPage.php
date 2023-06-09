<?php

abstract class CRUDPage extends BasePage
{
    public const STATE_FORM_REQUESTED = 0;
    public const STATE_DATA_SENT = 1;

    public const ACTION_INSERT = "insert";
    public const ACTION_UPDATE = "update";
    public const ACTION_DELETE = "delete";
    public const ACTION_CHANGEPASSWORD = "change password";

    protected function redirect(string $action, bool $success, ?string $differentLocation = null) : void
    {
        $data = [
            'action' => $action,
            'success' => $success ? 1 : 0
        ];
        if($differentLocation == null)
            header('Location: list.php?' . http_build_query($data) );
        else
            header('Location: '.$differentLocation . http_build_query($data));

        exit;
    }
}