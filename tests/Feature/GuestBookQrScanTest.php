<?php

namespace Tests\Feature;

use App\Models\GuestBook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestBookQrScanTest extends TestCase
{
    use RefreshDatabase;

    public function test_numeric_qr_is_matched_as_student_identity(): void
    {
        $student = $this->student('Siswa Nomor', '0105024129');

        $this->postJson(route('guest-book.qr-scan'), ['qr_content' => '0105024129'])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('registered', true)
            ->assertJsonPath('qr_code', '0105024129')
            ->assertJsonPath('data.nomor_identitas', $student->nomor_identitas);

        $this->assertDatabaseHas('guest_books', [
            'nama' => $student->name,
            'metode_absensi' => 'QR Code',
        ]);
    }

    public function test_structured_qr_prefers_nisn_then_matches_identity_column(): void
    {
        $student = $this->student('DAVINO NABIL ARVAREZA', '0084674092');
        $qr = "DAVINO NABIL ARVAREZA\nNISN: 0084674092\nNIS: 12584/1258.066\nAlamat: Contoh";

        $this->postJson(route('guest-book.qr-scan'), ['qr_content' => $qr])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('registered', true)
            ->assertJsonPath('qr_code', $qr)
            ->assertJsonPath('data.nama', $student->name)
            ->assertJsonPath('data.nomor_identitas', $student->nomor_identitas);
    }

    public function test_unknown_qr_is_reported_without_creating_a_student(): void
    {
        $this->postJson(route('guest-book.qr-scan'), ['qr_content' => '9912345678'])
            ->assertNotFound()
            ->assertJsonPath('decoded', true)
            ->assertJsonPath('success', false)
            ->assertJsonPath('registered', false)
            ->assertJsonPath('qr_code', '9912345678');

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('guest_books', 0);
    }

    private function student(string $name, string $identity): User
    {
        return User::create([
            'name' => $name,
            'nomor_identitas' => $identity,
            'kelas' => 'X RPL 1',
            'jurusan' => 'RPL',
            'password' => 'test-password',
            'role' => 'siswa',
        ]);
    }
}
