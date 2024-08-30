<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use App\Models\Category;
use App\Models\User;
use App\Services\CategoryService;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\LengthAwarePaginator;
class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $categoryService;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class); // Utilisez le namespace complet ici
        $this->categoryService = $this->createMock(CategoryService::class);
    }

    /** @test */
    public function it_can_get_categories_with_filters()
    {
        // Créer un utilisateur avec les permissions nécessaires
        $user = User::factory()->create();
        $user->givePermissionTo('view categories'); // Assurez-vous que l'utilisateur a la permission nécessaire

        // Authentifier l'utilisateur
        Auth::login($user);

        // Créer des catégories pour tester
        $categories = Category::factory()->count(15)->create();

        // Créer une instance de LengthAwarePaginator
        $currentPage = 1;
        $perPage = 10;
        $total = $categories->count();

        $paginator = new LengthAwarePaginator(
            $categories->forPage($currentPage, $perPage),
            $total,
            $perPage,
            $currentPage,
            ['path' => url('/api/categories')]
        );

        // Simuler la méthode getAllCategories pour retourner le paginator
        $this->categoryService->method('getAllCategories')
            ->willReturn($paginator);

        $response = $this->getJson('/api/categories?per_page=10');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'items' => [
                '*' => ['id', 'name', 'created_at']
            ],
        
        ]);
    }



    /** @test */
    public function it_returns_unauthorized_when_user_cannot_view_categories()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->categoryService->expects($this->never())
            ->method('getAllCategories');

        $response = $this->getJson('/api/categories');

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_create_a_category()
    {
        $admin = User::factory()->admin()->create();
        Auth::login($admin);

        $response = $this->postJson('/api/categories', [
            'name' => 'New Category',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id', 'name', 'created_at'
        ]);
    }

    /** @test */
    public function it_returns_error_if_unauthorized_to_create_category()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $response = $this->postJson('/api/categories', [
            'name' => 'New Category',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_update_a_category()
    {
        $admin = User::factory()->admin()->create();
        Auth::login($admin);

        $category = Category::factory()->create();

        $response = $this->patchJson("/api/categories/{$category->id}", [
            'name' => 'Updated Category',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
           
                'id' => $category->id,
                'name' => 'Updated Category'
            
        ]);
    }

    /** @test */
    public function it_returns_error_if_unauthorized_to_update_category()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $category = Category::factory()->create();

        $response = $this->patchJson("/api/categories/{$category->id}", [
            'name' => 'Updated Category',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_delete_a_category()
    {
        $admin = User::factory()->admin()->create();
        Auth::login($admin);

        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Category deleted successfully'
        ]);
    }

    /** @test */
    public function it_returns_error_if_unauthorized_to_delete_category()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_get_single_category()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view categories');
        Auth::login($user);

        $category = Category::factory()->create();

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id', 'name', 'created_at'
        ]);
    }

    /** @test */
    public function it_returns_error_if_category_not_found()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view categories');
        Auth::login($user);

        $response = $this->getJson('/api/categories/999');

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Category not found'
        ]);
    }

    /** @test */
    public function it_returns_unauthorized_when_user_cannot_view_category()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $category = Category::factory()->create();

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(403);
    }
}
