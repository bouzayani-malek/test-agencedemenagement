<?php

namespace App\Custom;

use Illuminate\Pagination\LengthAwarePaginator;

class CustomPaginator extends LengthAwarePaginator
{
    /**
     * Crée une nouvelle instance de la pagination.
     *
     * @param  array  $items
     * @param  int  $total
     * @param  int  $perPage
     * @param  int  $currentPage
     * @param  array  $options
     * @return void
     */
    public function __construct($items, $total, $perPage, $currentPage = null, array $options = [])
    {
        parent::__construct($items, $total, $perPage, $currentPage, $options);
    }

    /**
     * Transformer la pagination en tableau.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'current_page' => $this->currentPage(),
            'total_pages' => $this->lastPage(),
            'per_page' => $this->perPage(),
            'total_items' => $this->total(),
            'items' => $this->items,
        ];
    }
}
