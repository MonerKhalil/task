<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;

class PaginationProcess
{
    private int $DEFAULT_PAGES_Count = 20;

    /**
     * @param $queryBuilder
     * @return mixed
     * @author moner khalil
     */
    public function dataPaginate($queryBuilder): mixed
    {
        $tempCount = $this->countItemsPaginate();

        if ($tempCount === "all"){
            $data = $queryBuilder->get();
        }else{
            $data = $queryBuilder->paginate($tempCount);
        }
        return $data;
    }

    /**
     * @return int|string
     * @author moner khalil
     */
    public function countItemsPaginate(): int|string
    {
        if ( isset(request()->countItems) &&
            (
                (is_numeric(request()->countItems) && request()->countItems >= 1)
                ||
                (request()->countItems == 'all')
            )
        ){
            return request()->countItems;
        }

        return $this->DEFAULT_PAGES_Count;
    }

    /**
     * @description check if is api => handle data response
     * @param mixed $collection
     * @param $mainData
     * @return array
     * @author moner khalil
     */
    public function responsePagination(mixed $collection,$mainData = null)
    {
        if (is_null($mainData) && $collection instanceof LengthAwarePaginator){
            $allQueryParams = request()->all();
            $paginate = $collection->appends($allQueryParams);
            return $this->mainArrayPaginate($paginate->items(),$paginate);
        }
        if ($mainData instanceof LengthAwarePaginator){
            $allQueryParams = request()->all();
            $paginate = $mainData->appends($allQueryParams);
            return $this->mainArrayPaginate($collection,$paginate);
        }
        return $collection;
    }

    private function mainArrayPaginate($items,$paginate){
        return [
            "items" => $items,
            "current_page" => $paginate->currentPage(),
            "url_next_page" => $paginate->nextPageUrl(),
            "url_pre_page" => $paginate->previousPageUrl(),
            "url_first_page" => $paginate->url(1),
            "url_last_page" => $paginate->url($paginate->lastPage()),
            "total_pages" => $paginate->lastPage(),
            "total_items" => $paginate->total(),
            "has_more_pages" => $paginate->hasMorePages(),
        ];
    }
}
