<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\KeluhanPelanggan;
use App\Models\KeluhanStatusHis;

class KeluhanStatusHisTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_status_by_keluhan_id()
    {
        // Arrange: buat keluhan dan beberapa status history
        $keluhan = KeluhanPelanggan::factory()->create();
        KeluhanStatusHis::factory()->count(3)->create([
            'keluhan_id' => $keluhan->id,
        ]);
        $this->assertDatabaseCount('keluhan_status_his', 3);

        // Act: panggil endpoint delete
        $response = $this->deleteJson('/api/keluhan-status-history/delete-by-keluhan/' . $keluhan->id);

        // Assert: response dan database
        $response->assertStatus(200);
        $this->assertDatabaseCount('keluhan_status_his', 0);
    }
} 