<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ProductImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([LaravelLocalizationRedirectFilter::class, LocaleSessionRedirect::class]);
        $this->seed(RolesPermissionsSeeder::class);
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin', 'email' => 'admin@example.test', 'password' => bcrypt('password'),
            'role_id' => Role::where('slug', Role::SUPER_ADMIN)->value('id'),
        ]);
    }

    private function spreadsheet(array $rows): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'medicine-import-');
        $sheet = new Spreadsheet();
        $sheet->getActiveSheet()->fromArray($rows);
        (new Xlsx($sheet))->save($path);

        return new UploadedFile($path, 'medicines.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    public function test_admin_can_import_a_valid_excel_file(): void
    {
        Category::create([
            'name' => json_encode(['en' => 'Pain relief', 'ar' => 'مسكنات'], JSON_UNESCAPED_UNICODE),
            'description' => json_encode(['en' => 'Pain medicines', 'ar' => 'أدوية مسكنة'], JSON_UNESCAPED_UNICODE),
        ]);
        $file = $this->spreadsheet([
            ['name_en', 'name_ar', 'price', 'quantity', 'category', 'barcode'],
            ['Paracetamol', 'باراسيتامول', 2.5, 100, 'Pain relief', '123456789'],
            ['Ibuprofen', 'ايبوبروفين', 4, 50, 'Pain relief', '987654321'],
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.products.import'), ['file' => $file])
            ->assertRedirect(route('admin.products.index'));

        $this->assertSame(2, Product::count());
        $this->assertDatabaseHas('products', ['barcode' => '123456789', 'quantity' => 100]);
    }

    public function test_invalid_row_rejects_the_entire_import(): void
    {
        $file = $this->spreadsheet([
            ['name_en', 'price', 'quantity'],
            ['Valid medicine', 4, 10],
            ['', 4, 10],
        ]);

        $this->actingAs($this->admin())
            ->from(route('admin.products.import.form'))
            ->post(route('admin.products.import'), ['file' => $file])
            ->assertRedirect(route('admin.products.import.form'));

        $this->assertSame(0, Product::count());
    }
}
