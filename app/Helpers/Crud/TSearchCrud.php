<?php

namespace App\Helpers\Crud;

trait TSearchCrud
{
    public function search(mixed $query){
        $filterSearch = is_array(request()->filter) ? request()->filter : [];
        $searchQuery = request()->searchQuery ?? null;
        $query = $this->normalSearch($searchQuery,$query);
        $query = $this->advancedSearch($filterSearch,$query);
        return $this->orderBy($query);
    }

    private function advancedSearch($filterSearch,mixed $query){
        $nameTable = $this->getNameTable();
        if (sizeof($filterSearch) > 0){
            foreach ($filterSearch as $key => $value){
                #only date or number...
                /*
                $value => [
                    "strategy" => ,
                    "value" => ,
                ]
                */
                #MultiSelect
                if (is_array($value) && !isset($value['strategy']) && !isset($value['value']) && in_array($key,$this->fieldsSearchMultiSelect())){
                    $query = $query->whereIn("{$nameTable}.{$key}",$value);
                }
                #Date
                elseif (is_array($value) && isset($value['strategy']) && isset($value['value']) && !is_array($value['value']) && in_array($key,$this->fieldsDate())){
                    $query = $query->whereDate("{$nameTable}.{$key}", $this->getStrategy($value['strategy']), $value['value']);
                }
                #Numirce
                elseif (is_array($value) && isset($value['strategy']) && isset($value['value']) && !is_array($value['value']) && in_array($key,$this->fieldsNumeric())){
                    $query = $query->where("{$nameTable}.{$key}", $this->getStrategy($value['strategy']), $value['value']);
                }
                #Like
                elseif (!is_array($value) && in_array($key,$this->fieldsSearchLike())){
                    $query = $query->where("{$nameTable}.{$key}", "LIKE", "%{$value}%");
                }
                #Default
                elseif (!is_array($value) && in_array($key,$this->allFields())){
                    $query = $query->where("{$nameTable}.{$key}",$value);
                }

            }
        }
        return $query;
    }

    private function normalSearch($searchQuery,mixed $query){
        $nameTable = $this->getNameTable();
        $fieldsSearchLike = $this->fieldsSearchLike();
        if (is_string($searchQuery) && sizeof($fieldsSearchLike) > 0){
            $query = $query->where(function ($whereQuery)use($fieldsSearchLike,$searchQuery,$nameTable){
                $whereQuery = $this->addSearch($searchQuery,$whereQuery);
                foreach ($fieldsSearchLike as $field){
                    $whereQuery->orWhere("{$nameTable}.{$field}","LIKE","%{$searchQuery}%");
                }
            });
        }else{
            $query = $this->addSearch($searchQuery,$query);
        }
        return $query;
    }

    protected function addSearch($searchQuery,mixed $query):mixed{
        return $query;
    }

    private function orderBy($query){
        $nameTable = $this->getNameTable();
        $orderFields = $this->orderFields();
        $orderBy = request()->orderBy;
        $sortBy = request()->sortBy;
        $sortBy = in_array($sortBy,["asc","desc"]) ? $sortBy : "desc";
        if (!is_null($orderBy) && in_array($orderBy,$orderFields)){
            return $query->orderBy("{$nameTable}.{$orderBy}",$sortBy);
        }
        return $query->orderBy("{$nameTable}.created_at",$sortBy);
    }

    private function getStrategy($strategy): string
    {
        return match ($strategy) {
            'gt' => '>=',
            'lt' => '<=',
            default => '=',
        };
    }

    private function allFields(){
        $fields = ["id"];
        return array_merge($fields,$this->fieldsSearchMultiSelect(),$this->fieldsDate(),$this->fieldsNumeric(),$this->fieldsSearchLike(),$this->fieldsBoolean());
    }
}
