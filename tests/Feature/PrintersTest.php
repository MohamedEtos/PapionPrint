<?php

namespace Tests\Feature;

use App\Models\Customers;
use App\Models\Machines;
use App\Models\Printers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PrintersTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user and log them in for all tests
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_order()
    {
        $response = $this->postJson(route('printers.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['customerId', 'machineId']);
    }

    /** @test */
    public function it_can_create_a_printer_order()
    {
        $customer = Customers::create(['name' => 'Test Customer']);
        $machine = Machines::create(['name' => 'Test Machine']);

        $data = [
            'customerId' => $customer->name, 
            'machineId' => $machine->id,
            'fileHeight' => 100,
            'fileWidth' => 50,
            'fileCopies' => 1,
            'picInCopies' => 1, 
            'meters' => 10, 
            'status' => 'بانتظار اجراء',
            'price' => 500,
        ];

        $response = $this->postJson(route('printers.store'), $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => 'Order created successfully']);

        $this->assertDatabaseHas('printers', [
            'machineId' => $machine->id,
            'fileHeight' => 100,
        ]);
    }

    /** @test */
    public function it_can_update_a_printer_order()
    {
        // Setup initial data
        $customer = Customers::create(['name' => 'Customer A']);
        $machine = Machines::create(['name' => 'Machine A']);
        
        $order = Printers::create([
            'orderNumber' => 'TEST-123',
            'customerId' => $customer->id,
            'machineId' => $machine->id,
            'status' => 'بانتظار اجراء',
            'designerId' => $this->user->id,
            'fileHeight' => 100, // Added
            'fileWidth' => 50,   // Added
            'fileCopies' => 1,   // Added
            'picInCopies' => 1,  // Added
            'meters' => 5
        ]);

        // Update data
        $data = [
            'customerId' => 'Customer B', 
            'meters' => 20,
            'status' => 'بدات الطباعة',
             // Validation likely requires these again if using same request class?
             // Or maybe they are nullable on update?
             // If controller validation checks them, we need them.
             // Usually update methods have 'sometimes' rules or strict rules.
             // I'll add them to be safe if validation fails.
            'machineId' => $machine->id,
            'fileHeight' => 100,
            'fileWidth' => 50,
            'fileCopies' => 1,
            'picInCopies' => 1,
        ];

        $response = $this->putJson(route('printers.update', $order->id), $data);

        $response->assertStatus(200);
        
        // Reload order
        $order->refresh();
        
        $this->assertEquals(20, $order->meters);
        $this->assertEquals('بدات الطباعة', $order->status);
        $this->assertNotEquals($customer->id, $order->customerId);
        $this->assertEquals('Customer B', $order->customers->name);
    }

    /** @test */
    public function it_can_delete_a_printer_order()
    {
        $customer = Customers::create(['name' => 'Delete Me']);
        $machine = Machines::create(['name' => 'Machine del']);
        
        $order = Printers::create([
            'orderNumber' => 'DEL-123',
            'customerId' => $customer->id,
            'machineId' => $machine->id,
            'status' => 'بانتظار اجراء',
            'designerId' => $this->user->id,
            'fileHeight' => 100, // Added
            'fileWidth' => 50,   // Added
            'fileCopies' => 1,   // Added
            'picInCopies' => 1,  // Added
            'meters' => 5
        ]);

        // Route is POST for printers.delete based on web.php
        $response = $this->postJson(route('printers.delete', $order->id));

        $response->assertStatus(200);
        
        // Assert Soft Deleted
        $this->assertSoftDeleted('printers', ['id' => $order->id]);
    }

    /** @test */
    public function it_prevents_updating_non_existent_order()
    {
        $response = $this->putJson(route('printers.update', 99999), []);
        // Validation handles "id" check via injected validator inside controller? 
        // Or if using route binding/findOrFail it returns 404.
        // My manual validation: Validator::make(['id' => $id], ['id' => 'exists...'])
        
        $response->assertStatus(422); // Validation error
    }
}
