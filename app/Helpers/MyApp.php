<?php

namespace App\Helpers;

use App\Helpers\Crud\CrudProcess;

class MyApp
{
    const PASSWORD = "P@ssw0rd@123@";

    private static $app = null;

    public ?FileProcess $fileProcess = null;
    public ?CrudProcess $crudProcess = null;
    public ?PaginationProcess $paginationProcess = null;
    public ?PermissionsProcess $permissionsProcess = null;

    private function __construct()
    {
        $this->crudProcess = new CrudProcess();
        $this->fileProcess = new FileProcess();
        $this->paginationProcess = new PaginationProcess();
        $this->permissionsProcess = new PermissionsProcess();
    }

    public static function main(){
        if (is_null(self::$app)){
            self::$app = new static();
        }
        return self::$app;
    }
}
