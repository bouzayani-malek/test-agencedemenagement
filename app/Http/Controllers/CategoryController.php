<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Custom\CustomPaginator;
use App\Services\CategoryService;
class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'per_page' => 'sometimes|integer|min:1',
            'search' => 'sometimes|string|max:255',
        ]);
         
        if (!Auth::user()->can('view categories')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        try {
            $categories = $this->categoryService->getAllCategories($request->query('per_page', 10), $request->query('search', ''));
            $customPaginator = new CustomPaginator(
                CategoryResource::collection($categories->items()),
                $categories->total(),
                $categories->perPage(),
                $categories->currentPage()
            );
            return response()->json($customPaginator);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('admin') || Auth::user()->cannot('create categories')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $category = $this->categoryService->createCategory($validated);
            return response()->json(new CategoryResource($category), 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    
    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasRole('admin') || Auth::user()->cannot('update categories')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        try { 
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $category = $this->categoryService->updateCategory($id, $validated);

            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }

            return response()->json(new CategoryResource($category), 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        if (!Auth::user()->hasRole('admin') || Auth::user()->cannot('delete categories')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        try {
            $isDeleted = $this->categoryService->deleteCategory($id);

            if (!$isDeleted) {
                return response()->json(['message' => 'Category not found'], 404);
            }

            return response()->json(['message' => 'Category deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        if (Auth::user()->cannot('view categories')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $category = $this->categoryService->getCategoryById($id);

            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }

            return response()->json(new CategoryResource($category), 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }


}
