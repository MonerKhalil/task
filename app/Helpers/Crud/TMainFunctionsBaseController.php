<?php

namespace App\Helpers\Crud;

use App\Helpers\MyApp;
use Illuminate\Http\UploadedFile;

trait TMainFunctionsBaseController
{
    use TSearchCrud;

    ########################################## Main Functions ##########################################

    protected abstract function getMainModel();

    protected abstract function storeRequest();

    protected abstract function editRequest();

    protected function getModel(){
        if (is_null($this->model)){
            $model = $this->getMainModel();
            $this->model = (new $model());
        }
        return $this->model;
    }

    protected function getQueryModel(){
        return $this->getMainModel()::query();
    }

    protected function getNameTable(){
        return $this->getModel()->getTable();
    }

    protected function fieldsSearchLike():array{
        return [];
    }

    protected function fieldsSearchMultiSelect():array{
        return [];
    }

    protected function orderFields():array{
        return ["created_at","id"];
    }

    protected function fieldsDate():array{
        return ["created_at"];
    }

    protected function fieldsNumeric():array{
        return [];
    }

    protected function fieldsFiles():array{
        return [];
    }

    protected function fieldsBoolean():array{
        return [];
    }

    protected function withRelations(): array{
        return [];
    }

    protected function resource():?string{
        return null;
    }

    protected function resolveDataStore($data){
        return $data;
    }

    protected function resolveDataUpdate($data,$objModel){
        return $data;
    }

    protected function creatingObserver($data){

    }

    protected function createdObserver($objModel,$data){

    }

    protected function updatingObserver($objModel,$data){

    }

    protected function updatedObserver($objModel,$data){

    }

    ##########################################/ Main Functions /##########################################

    private function resolveDataFiles(array $data): array{
        foreach ($data as $key => $datum){
            if (in_array($key,$this->fieldsFiles()) && $datum instanceof UploadedFile){
                $data[$key] = MyApp::main()->fileProcess->uploadFile($datum,$this->getNameTable());
            }
        }
        return $data;
    }
}
