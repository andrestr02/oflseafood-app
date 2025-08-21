<?php

namespace Tests\Unit;

use Tests\TestCase; // <-- gunakan TestCase milik Laravel, bukan PHPUnit
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Supplier;
use App\Models\Purchase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function purchase_can_be_created_with_correct_payment_status()
    {
        $supplier = Supplier::factory()->create();

        // 1. unpaid
        $purchase = Purchase::create([
            'supplier_id'   => $supplier->id,
            'date'           => now()->toDateString(),
            'total_price'   => 100000,
            'amount_paid'   => 0,
            'payment_status' => 'unpaid',
        ]);

        $this->assertEquals(100000, $purchase->total_price);
        $this->assertEquals(0, $purchase->amount_paid);
        $this->assertEquals(100000, $purchase->amount_due);
        $this->assertEquals('unpaid', $purchase->payment_status);

        // 2. partial
        $purchase->update(['amount_paid' => 40000]);
        $purchase->refresh();

        $this->assertEquals(60000, $purchase->amount_due);
        $this->assertEquals('partial', $purchase->payment_status);

        // 3. paid
        $purchase->update(['amount_paid' => 100000]);
        $purchase->refresh();

        $this->assertEquals(0, $purchase->amount_due);
        $this->assertEquals('paid', $purchase->payment_status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function invoice_number_is_generated_automatically()
    {
        $supplier = Supplier::factory()->create();

        $purchase = Purchase::create([
            'supplier_id' => $supplier->id,
            'total_price' => 50000,
            'amount_paid' => 0,
            'date'        => now(),
        ]);

        $this->assertStringStartsWith('INV-', $purchase->invoice_number);
        $this->assertMatchesRegularExpression('/^INV-\d{8}-\d{4}$/', $purchase->invoice_number);
    }
}
