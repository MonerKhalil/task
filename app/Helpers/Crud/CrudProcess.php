<?php

namespace App\Helpers\Crud;

use Illuminate\Support\Facades\Route;

class CrudProcess
{
    public function routesCrud(string $prefix,string $controller,array $middlewares = [],bool $withRouteActive = false){
        Route::prefix($prefix)->middleware($middlewares)->controller($controller)
            ->group(function ()use($withRouteActive){
            Route::get('index', 'index');
            Route::get('show/{id}', 'find');
            Route::post('store', 'store');
            Route::post('update/{id}', 'update');
            Route::delete('delete', 'delete');
            if ($withRouteActive){
                Route::put('change-active-status/{id}', 'changeActiveStatus');
            }
        });
    }

    public function ruleImage($isRequired = true,array $mimes = null){
        $rules = ["image"];
        if (is_null($mimes)){
            $rules[] = "mimes:png,jpg,jpeg,gif,svg";
        }else{
            $rules[] = "mimes:" . implode(",",$mimes);
        }
        $rules[] = $isRequired ? "required" : "nullable";
        return $rules;
    }

    public function ruleFile($isRequired = true,$mimes = null){
        $rules = ["file"];
        if (is_null($mimes)){
            $rules[] = "mimes:pdf,xlsx,docx,csv";
        }else{
            $rules[] = "mimes:" . implode(",",$mimes);
        }
        $rules[] = $isRequired ? "required" : "nullable";
        return $rules;
    }
}
