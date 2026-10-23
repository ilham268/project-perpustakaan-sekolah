<?php

namespace Tests\Feature;

use App\Models\TeacherGuestBook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class TeacherManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_be_created_without_student_identity_or_password(): void
    {
        $this->asAdmin();

        $this->post(route('users.store'), [
            'name' => 'Ahmad Guru',
            'role' => 'guru',
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'Ahmad Guru',
            'role' => 'guru',
            'nomor_identitas' => null,
            'kelas' => null,
            'jurusan' => null,
        ]);
    }

    public function test_teacher_excel_import_reads_names_and_skips_duplicates_on_reimport(): void
    {
        $this->asAdmin();
        $file = $this->teacherWorkbook();

        $this->post(route('users.import-excel'), [
            'jenis_import' => 'guru',
            'file' => $file,
        ])->assertRedirect(route('users.index'))
            ->assertSessionHas('success', fn ($message) => str_contains($message, 'Berhasil: 3 guru'));

        $this->assertDatabaseCount('users', 4); // termasuk admin
        $this->assertDatabaseHas('users', ['name' => 'AHMAD', 'role' => 'guru', 'nomor_identitas' => null]);

        $this->post(route('users.import-excel'), [
            'jenis_import' => 'guru',
            'file' => $this->teacherWorkbook(),
        ])->assertRedirect(route('users.index'))
            ->assertSessionHas('success', fn ($message) => str_contains($message, 'Dilewati: 3 data duplikat'));

        $this->assertSame(3, User::where('role', 'guru')->count());
    }

    public function test_student_excel_import_still_uses_nisn_and_the_selected_class(): void
    {
        $this->asAdmin();
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->setTitle('RPL');
        $spreadsheet->getActiveSheet()->fromArray([['NISN', 'NAMA'], ['0105024129', 'Siswa Uji']], null, 'A1');
        $path = tempnam(sys_get_temp_dir(), 'student-import-') . '.xlsx';
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        $this->post(route('users.import-excel'), [
            'jenis_import' => 'siswa',
            'kelas' => 'X',
            'file' => new UploadedFile($path, 'siswa.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true),
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'Siswa Uji', 'nomor_identitas' => '0105024129',
            'kelas' => 'X', 'jurusan' => 'RPL', 'role' => 'siswa',
        ]);
    }

    public function test_teacher_guest_book_search_and_store_use_a_real_teacher_user(): void
    {
        $teacher = User::create([
            'name' => 'Ahmad Guru', 'nomor_identitas' => null, 'kelas' => null, 'jurusan' => null,
            'password' => 'unused-password', 'role' => 'guru',
        ]);

        $this->getJson(route('teacher-guest-book.create', ['q' => 'Ahmad']))
            ->assertOk()->assertJsonFragment(['id' => $teacher->id, 'name' => 'Ahmad Guru']);

        $this->post(route('teacher-guest-book.store'), [
            'user_id' => $teacher->id,
            'keperluan' => 'Rapat',
        ])->assertRedirect(route('teacher-guest-book.create'));

        $this->assertDatabaseHas('teacher_guest_books', [
            'user_id' => $teacher->id,
            'nama' => 'Ahmad Guru',
            'keperluan' => 'Rapat',
            'metode_absensi' => 'manual',
        ]);

        $student = User::create([
            'name' => 'Bukan Guru', 'nomor_identitas' => '0123456789', 'kelas' => 'X', 'jurusan' => 'RPL',
            'password' => 'unused-password', 'role' => 'siswa',
        ]);
        $this->from(route('teacher-guest-book.create'))
            ->post(route('teacher-guest-book.store'), ['user_id' => $student->id, 'keperluan' => 'Rapat'])
            ->assertSessionHasErrors('user_id');
        $this->assertSame(1, TeacherGuestBook::count());
    }

    private function asAdmin(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin', 'nomor_identitas' => '0000000001', 'password' => 'password', 'role' => 'admin',
        ]));
    }

    private function teacherWorkbook(): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Guru');
        $sheet->fromArray([['No', 'NAMA', 'Keterangan'], [1, '  AHMAD  ', 'wali kelas'], [2, 'BUDI', ''], [3, 'SITI', '']], null, 'A1');
        $path = tempnam(sys_get_temp_dir(), 'teacher-import-') . '.xlsx';
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return new UploadedFile($path, 'guru.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }
}
