<?php

namespace App\Http\Controllers;

use App\Exceptions\WrongStateException;
use App\Helpers\Crud\TMainFunctionsBaseController;
use App\Helpers\MyApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class BaseCrudController extends Controller
{
    use TMainFunctionsBaseController;

    private mixed $model = null;

    protected function queryGet(){
        $query = $this->getQueryModel();
        $relations = $this->withRelations();
        return sizeof($relations)>0 ? $query->with($relations) : $query;
    }

    public function index(callable $callback = null){
        $query = $this->queryGet();
        if (!is_null($callback)){
            $query = $callback($query);
        }
        $query = $this->search($query);
        $data = MyApp::main()->paginationProcess->dataPaginate($query);
        $data = !is_null($this->resource()) ?
            MyApp::main()->paginationProcess->responsePagination($this->resource()::collection($data),$data)
            :
            MyApp::main()->paginationProcess->responsePagination($data);
        return $this->responseSuccess([
            $this->getNameTable() => $data,
        ]);
    }

    public function find($id){
        $nameTable = $this->getNameTable();
        $result = $this->queryGet()->where("{$nameTable}.id",$id)->firstOrFail();
        $result = !is_null($this->resource()) ? $this->resource()::make($result) : $result;
        return $this->responseSuccess(compact("result"));
    }

    public function store(Request $request){
        $data = app()->make($this->storeRequest())->validated();
        $data = $this->resolveDataStore($data);
        try {
            DB::beginTransaction();
            $this->creatingObserver($data);
            $data = $this->resolveDataFiles($data);
            $result = $this->getQueryModel()->create($data);
            $this->createdObserver($result,$data);
            $result = !is_null($this->resource()) ? $this->resource()::make($result) : $result;
            DB::commit();
            return $this->responseSuccess(compact("result"),"Item created successfully.");
        }catch (\Exception $exception){
            DB::rollBack();
            throw new WrongStateException($exception->getMessage());
        }
    }

    public function update($id,Request $request){
        $data = app()->make($this->editRequest())->validated();
        $nameTable = $this->getNameTable();
        $result = $this->getQueryModel()->where("{$nameTable}.id",$id)->firstOrFail();
        $data = $this->resolveDataUpdate($data,$result);
        try {
            DB::beginTransaction();
            $this->updatingObserver($result,$data);
            $data = $this->resolveDataFiles($data);
            $result->update($data);
            $this->updatedObserver($result,$data);
            $result = !is_null($this->resource()) ? $this->resource()::make($result) : $result;
            DB::commit();
            return $this->responseSuccess(compact("result"),"Item updated successfully.");
        }catch (\Exception $exception){
            DB::rollBack();
            throw new WrongStateException($exception->getMessage());
        }
    }

    public function delete(Request $request){
        $request->validate([
            "ids" => ["required","array"],
            "ids.*" => ["required","integer"],
        ]);
        $ids = $request->ids;
        $nameTable = $this->getNameTable();
        $this->getQueryModel()
            ->whereIn("{$nameTable}.id",$ids)
            ->delete();
        return $this->responseSuccess([],"Items deleted successfully.");
    }

    public function changeActiveStatus($id){
        $nameTable = $this->getNameTable();
        $result = $this->getQueryModel()->where("{$nameTable}.id",$id)->firstOrFail();
        $result->update([
            "is_active" => !$result->is_active,
        ]);
        return $this->responseSuccess([],"Item changed active status successfully.");
    }
}
