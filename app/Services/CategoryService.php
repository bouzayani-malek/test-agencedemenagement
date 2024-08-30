<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function getAllCategories($perPage, $search)
    {
        $query = Category::where('is_deleted', 0);

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->paginate($perPage);
    }

    public function createCategory($data)
    {
        return Category::create($data);
    }

    public function updateCategory($id, $data)
    {
        $category = Category::where('id', $id)->where('is_deleted', 0)->first();
        if ($category) {
            $category->update($data);
            return $category;
        }

        return null;
    }

    public function deleteCategory($id)
    {
        $category = Category::where('id', $id)->where('is_deleted', 0)->first();
        if ($category) {
            $category->is_deleted = 1;
            $category->save();
            return true;
        }

        return false;
    }

    public function getCategoryById($id)
    {
        return Category::where('id', $id)->where('is_deleted', 0)->first();
    }
}
