<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HelpController extends Controller
{
    public function index()
    {
        $role      = Auth::user()->role;
        $roleLabel = match ($role) {
            'admin_ga' => 'Admin GA',
            'approver' => 'Approver',
            default    => 'Staff',
        };

        $sections = $this->getFaqSections($role);

        return view('help.index', compact('role', 'roleLabel', 'sections'));
    }

    private function getFaqSections(string $role): array
    {
        $all      = ['staff', 'approver', 'admin_ga'];
        $approver = ['approver'];
        $admin    = ['admin_ga'];

        $sections = [

            /* ══════════════════════════════════════════
             |  SEMUA ROLE
             ══════════════════════════════════════════ */

            [
                'id'    => 'pengajuan',
                'title' => 'Mengajukan Sewa Kendaraan',
                'icon'  => 'car',
                'color' => 'blue',
                'roles' => $all,
                'items' => [
                    [
                        'q' => 'Bagaimana cara mengajukan sewa kendaraan?',
                        'a' => 'Klik menu <strong>Ajukan Sewa</strong> di navbar. Isi form pengajuan secara lengkap — tujuan perjalanan, tanggal dan jam keberangkatan, estimasi kembali, dan jumlah penumpang. Pilih kendaraan yang tersedia, lalu klik <strong>Kirim Pengajuan</strong>. Pengajuan akan langsung masuk ke antrian approver.',
                    ],
                    [
                        'q' => 'Bagaimana cara memilih kendaraan yang tersedia?',
                        'a' => 'Sistem secara otomatis menampilkan kendaraan yang <strong>tersedia</strong> sesuai tanggal dan jam yang Anda pilih. Kendaraan yang sedang digunakan atau statusnya tidak aktif tidak akan muncul sebagai pilihan.',
                    ],
                    [
                        'q' => 'Bisakah saya membatalkan pengajuan yang sudah dikirim?',
                        'a' => 'Selama status masih <span class="bs bs-yellow">Menunggu Persetujuan</span>, Anda bisa membatalkan dari halaman Beranda. Pengajuan yang sudah berstatus <span class="bs bs-blue">Disetujui Atasan</span> atau lebih lanjut tidak bisa dibatalkan sendiri — hubungi Admin GA.',
                    ],
                    [
                        'q' => 'Bisakah saya mengubah data pengajuan setelah dikirim?',
                        'a' => 'Pengajuan yang sudah terkirim tidak dapat diedit langsung. Batalkan pengajuan tersebut (selama masih <span class="bs bs-yellow">Menunggu Persetujuan</span>), lalu buat pengajuan baru dengan data yang sudah diperbaiki.',
                    ],
                ],
            ],

            [
                'id'    => 'status',
                'title' => 'Memahami Status Pengajuan',
                'icon'  => 'info',
                'color' => 'indigo',
                'roles' => $all,
                'items' => [
                    [
                        'q' => 'Apa arti masing-masing status pengajuan?',
                        'a' => '<div class="status-grid">
                                    <div class="status-row"><span class="bs bs-yellow">Menunggu Persetujuan</span><span>Pengajuan terkirim dan menunggu keputusan approver.</span></div>
                                    <div class="status-row"><span class="bs bs-blue">Disetujui Atasan</span><span>Disetujui approver, menunggu Admin GA menetapkan kendaraan.</span></div>
                                    <div class="status-row"><span class="bs bs-red">Ditolak</span><span>Pengajuan tidak disetujui. Buka detail untuk melihat alasannya.</span></div>
                                    <div class="status-row"><span class="bs bs-purple">Unit Siap</span><span>Admin GA telah menetapkan kendaraan. Siap digunakan sesuai jadwal.</span></div>
                                    <div class="status-row"><span class="bs bs-green">Sedang Jalan</span><span>Perjalanan sedang berlangsung.</span></div>
                                    <div class="status-row"><span class="bs bs-gray">Selesai</span><span>Perjalanan selesai dan kendaraan sudah kembali.</span></div>
                                    <div class="status-row"><span class="bs bs-orange">Pengajuan Batal</span><span>Permintaan pembatalan dikirim, menunggu konfirmasi Admin GA.</span></div>
                                    <div class="status-row"><span class="bs bs-red">Dibatalkan</span><span>Pengajuan telah resmi dibatalkan.</span></div>
                                </div>',
                    ],
                    [
                        'q' => 'Bagaimana cara melihat status pengajuan saya?',
                        'a' => 'Buka halaman <strong>Beranda</strong>. Semua pengajuan Anda beserta status terkininya ditampilkan di sana. Klik salah satu pengajuan untuk melihat detail lengkap termasuk riwayat perubahan status.',
                    ],
                    [
                        'q' => 'Apa yang harus dilakukan jika pengajuan saya ditolak?',
                        'a' => 'Buka detail pengajuan yang ditolak untuk membaca alasan penolakan dari approver. Jika ingin mengajukan ulang, buat pengajuan baru dengan melakukan perbaikan sesuai catatan yang diberikan.',
                    ],
                    [
                        'q' => 'Apa beda status "Pengajuan Batal" dengan "Dibatalkan"?',
                        'a' => '<span class="bs bs-orange">Pengajuan Batal</span> berarti Anda sudah mengajukan permintaan pembatalan namun Admin GA belum mengkonfirmasinya. Setelah Admin GA mengkonfirmasi, status berubah menjadi <span class="bs bs-red">Dibatalkan</span>.',
                    ],
                    [
                        'q' => 'Mengapa pengajuan saya masih berstatus "Menunggu Persetujuan" sudah lama?',
                        'a' => 'Bisa jadi approver belum sempat memproses pengajuan Anda. Anda bisa menghubungi approver secara langsung untuk mengingatkan, atau hubungi Admin GA jika diperlukan tindakan lebih lanjut.',
                    ],
                ],
            ],

            [
                'id'    => 'notifikasi',
                'title' => 'Notifikasi',
                'icon'  => 'bell',
                'color' => 'yellow',
                'roles' => $all,
                'items' => [
                    [
                        'q' => 'Notifikasi apa saja yang akan saya terima?',
                        'a' => 'Anda akan menerima notifikasi untuk:<ul class="faq-list"><li>Konfirmasi pengajuan berhasil terkirim</li><li>Pengajuan disetujui atau ditolak oleh approver</li><li>Kendaraan sudah disiapkan (status Unit Siap)</li><li>Perjalanan dimulai dan selesai</li><li>Pengajuan dibatalkan</li></ul>',
                    ],
                    [
                        'q' => 'Di mana saya bisa melihat notifikasi?',
                        'a' => 'Klik ikon <strong>lonceng (🔔)</strong> di pojok kanan navbar. Panel notifikasi akan muncul dari sisi kanan layar. Notifikasi yang belum dibaca ditandai dengan <strong>titik biru</strong> dan latar biru muda.',
                    ],
                    [
                        'q' => 'Bagaimana cara menandai notifikasi sebagai sudah dibaca?',
                        'a' => 'Klik notifikasi tersebut — otomatis tandai sebagai dibaca. Atau klik tombol <strong>Baca Semua</strong> di bagian atas panel notifikasi untuk menandai semua sekaligus.',
                    ],
                ],
            ],

            [
                'id'    => 'akun',
                'title' => 'Akun dan Profil',
                'icon'  => 'user',
                'color' => 'gray',
                'roles' => $all,
                'items' => [
                    [
                        'q' => 'Bagaimana cara mengubah data profil?',
                        'a' => 'Klik nama Anda di pojok kanan navbar (desktop) atau buka sidebar (mobile), lalu pilih <strong>Profil Saya</strong>. Dari halaman tersebut Anda dapat mengubah nama dan email.',
                    ],
                    [
                        'q' => 'Bagaimana cara mengganti password?',
                        'a' => 'Buka halaman <strong>Profil Saya</strong>, gulir ke bagian <em>Ubah Password</em>. Masukkan password lama, isi password baru dan konfirmasinya, lalu klik <strong>Simpan</strong>.',
                    ],
                    [
                        'q' => 'Bagaimana cara logout dari aplikasi?',
                        'a' => 'Klik nama Anda di navbar (desktop) lalu pilih <strong>Log Out</strong>. Pada perangkat mobile, buka sidebar dan scroll ke bawah untuk menemukan tombol <strong>Log Out</strong> berwarna merah.',
                    ],
                ],
            ],

            /* ══════════════════════════════════════════
             |  APPROVER
             ══════════════════════════════════════════ */

            [
                'id'    => 'persetujuan',
                'title' => 'Memproses Persetujuan',
                'icon'  => 'check-circle',
                'color' => 'green',
                'roles' => $approver,
                'items' => [
                    [
                        'q' => 'Di mana saya bisa melihat pengajuan yang perlu disetujui?',
                        'a' => 'Buka menu <strong>Persetujuan</strong> di navbar. Badge merah pada menu tersebut menunjukkan jumlah pengajuan yang menunggu keputusan Anda.',
                    ],
                    [
                        'q' => 'Bagaimana cara menyetujui pengajuan?',
                        'a' => 'Buka detail pengajuan, periksa semua informasi dengan teliti, lalu klik tombol <strong>Setujui</strong>. Sistem akan otomatis mengubah status menjadi <span class="bs bs-blue">Disetujui Atasan</span> dan mengirim notifikasi ke pemohon.',
                    ],
                    [
                        'q' => 'Bagaimana cara menolak pengajuan?',
                        'a' => 'Buka detail pengajuan, klik tombol <strong>Tolak</strong>, lalu isi kolom alasan penolakan. Alasan ini akan dikirimkan ke pemohon melalui notifikasi agar mereka tahu apa yang perlu diperbaiki.',
                    ],
                    [
                        'q' => 'Apakah wajib mengisi alasan saat menolak pengajuan?',
                        'a' => 'Sangat disarankan untuk selalu mengisi alasan penolakan. Alasan yang jelas membantu pemohon memahami kenapa pengajuannya ditolak dan apa yang perlu diperbaiki di pengajuan berikutnya.',
                    ],
                    [
                        'q' => 'Bisakah saya membatalkan keputusan yang sudah diberikan?',
                        'a' => 'Keputusan yang sudah diberikan tidak dapat diubah langsung dari halaman Persetujuan. Jika ada kesalahan keputusan, segera hubungi Admin GA untuk melakukan koreksi secara manual.',
                    ],
                    [
                        'q' => 'Bagaimana jika notifikasi pengajuan baru tidak masuk?',
                        'a' => 'Cek dulu panel notifikasi dengan mengklik ikon lonceng. Jika badge pada menu Persetujuan menunjukkan angka namun notifikasi tidak muncul, coba refresh halaman. Pastikan juga koneksi internet stabil.',
                    ],
                ],
            ],

            [
                'id'    => 'laporan-approver',
                'title' => 'Laporan Approver',
                'icon'  => 'chart',
                'color' => 'indigo',
                'roles' => $approver,
                'items' => [
                    [
                        'q' => 'Bagaimana cara melihat riwayat keputusan saya?',
                        'a' => 'Buka menu <strong>Laporan</strong> di navbar. Di sana tersedia riwayat semua keputusan yang pernah Anda buat, lengkap dengan tanggal, nama pemohon, kendaraan, dan keputusan yang diberikan.',
                    ],
                    [
                        'q' => 'Bagaimana cara mengekspor laporan riwayat persetujuan?',
                        'a' => 'Di halaman <strong>Laporan</strong>, atur filter periode atau status yang diinginkan, lalu klik tombol <strong>Ekspor Excel</strong>. File akan langsung diunduh ke perangkat Anda.',
                    ],
                    [
                        'q' => 'Apakah saya bisa melihat pengajuan dari approver lain?',
                        'a' => 'Tidak. Halaman Persetujuan dan Laporan hanya menampilkan pengajuan yang ditugaskan kepada akun Anda. Untuk data keseluruhan, hubungi Admin GA.',
                    ],
                ],
            ],

            /* ══════════════════════════════════════════
             |  ADMIN GA
             ══════════════════════════════════════════ */

            [
                'id'    => 'persiapan',
                'title' => 'Menu Persiapan (Dispatch)',
                'icon'  => 'clipboard',
                'color' => 'blue',
                'roles' => $admin,
                'items' => [
                    [
                        'q' => 'Apa itu menu Persiapan?',
                        'a' => 'Menu <strong>Persiapan</strong> menampilkan semua pengajuan berstatus <span class="bs bs-blue">Disetujui Atasan</span> yang belum ditetapkan kendaraannya. Tugas Anda di sini adalah memilih kendaraan internal atau vendor eksternal untuk setiap pengajuan.',
                    ],
                    [
                        'q' => 'Bagaimana cara menetapkan kendaraan internal?',
                        'a' => 'Buka pengajuan di menu <strong>Persiapan</strong>, pilih opsi <strong>Kendaraan Internal</strong>, pilih unit yang tersedia dari daftar, lalu konfirmasi. Status berubah menjadi <span class="bs bs-purple">Unit Siap</span> dan pemohon menerima notifikasi.',
                    ],
                    [
                        'q' => 'Bagaimana cara menggunakan vendor/kendaraan eksternal?',
                        'a' => 'Di menu <strong>Persiapan</strong>, buka pengajuan dan pilih opsi <strong>Vendor Eksternal</strong>. Isi data vendor (nama, nomor plat, dll.) lalu konfirmasi. Status akan berubah ke <span class="bs bs-purple">Unit Siap</span>.',
                    ],
                    [
                        'q' => 'Bagaimana cara memulai perjalanan?',
                        'a' => 'Setelah kendaraan ditetapkan (status <span class="bs bs-purple">Unit Siap</span>), klik tombol <strong>Mulai Perjalanan</strong>. Status berubah menjadi <span class="bs bs-green">Sedang Jalan</span> dan pengajuan berpindah ke menu <strong>Pantau</strong>.',
                    ],
                    [
                        'q' => 'Bagaimana cara mengkonfirmasi pembatalan vendor eksternal?',
                        'a' => 'Jika vendor membatalkan, buka pengajuan terkait di menu Persiapan dan klik <strong>Konfirmasi Batal Vendor</strong>. Pengajuan akan dikembalikan ke status yang sesuai dan pemohon mendapat notifikasi.',
                    ],
                    [
                        'q' => 'Apa arti badge merah di menu Persiapan?',
                        'a' => 'Badge merah menunjukkan jumlah pengajuan berstatus <span class="bs bs-blue">Disetujui Atasan</span> yang <strong>belum</strong> ditetapkan kendaraannya. Angka diperbarui otomatis setiap 10 detik.',
                    ],
                ],
            ],

            [
                'id'    => 'pantau',
                'title' => 'Menu Pantau (Perjalanan Aktif)',
                'icon'  => 'map',
                'color' => 'green',
                'roles' => $admin,
                'items' => [
                    [
                        'q' => 'Apa itu menu Pantau?',
                        'a' => 'Menu <strong>Pantau</strong> menampilkan semua perjalanan yang sedang aktif — berstatus <span class="bs bs-purple">Unit Siap</span> maupun <span class="bs bs-green">Sedang Jalan</span>. Dari sini Anda bisa memantau dan menyelesaikan perjalanan.',
                    ],
                    [
                        'q' => 'Bagaimana cara menyelesaikan perjalanan?',
                        'a' => 'Buka pengajuan aktif di menu <strong>Pantau</strong>, klik tombol <strong>Selesaikan Perjalanan</strong> setelah kendaraan kembali. Status berubah menjadi <span class="bs bs-gray">Selesai</span> dan kendaraan kembali tersedia untuk pengajuan lain.',
                    ],
                    [
                        'q' => 'Apa arti badge di menu Pantau?',
                        'a' => 'Badge merah menunjukkan jumlah perjalanan berstatus <span class="bs bs-purple">Unit Siap</span> dan <span class="bs bs-green">Sedang Jalan</span> yang masih aktif. Angka diperbarui otomatis setiap 10 detik.',
                    ],
                    [
                        'q' => 'Bagaimana jika kendaraan terlambat kembali?',
                        'a' => 'Pantau status di menu Pantau. Anda dapat menghubungi pengguna kendaraan secara langsung. Setelah kendaraan kembali dan kondisinya sudah diperiksa, klik <strong>Selesaikan Perjalanan</strong> untuk memperbarui status.',
                    ],
                ],
            ],

            [
                'id'    => 'unit',
                'title' => 'Manajemen Unit Kendaraan',
                'icon'  => 'truck',
                'color' => 'purple',
                'roles' => $admin,
                'items' => [
                    [
                        'q' => 'Bagaimana cara menambahkan kendaraan baru?',
                        'a' => 'Buka menu <strong>Unit</strong>, klik tombol <strong>Tambah Kendaraan</strong>. Isi data kendaraan meliputi nomor plat, jenis, merek dan model, kapasitas penumpang, dan status awal. Klik <strong>Simpan</strong>.',
                    ],
                    [
                        'q' => 'Bagaimana cara mengubah data kendaraan?',
                        'a' => 'Di halaman <strong>Unit</strong>, cari kendaraan yang ingin diubah lalu klik ikon <strong>Edit</strong>. Ubah data yang diperlukan dan klik <strong>Simpan Perubahan</strong>.',
                    ],
                    [
                        'q' => 'Bagaimana cara menonaktifkan kendaraan yang sedang dalam perbaikan?',
                        'a' => 'Buka data kendaraan tersebut, ubah statusnya menjadi tidak aktif, lalu simpan. Kendaraan dengan status tidak aktif tidak akan muncul sebagai pilihan pada form pengajuan.',
                    ],
                    [
                        'q' => 'Bagaimana cara menghapus kendaraan dari sistem?',
                        'a' => 'Buka data kendaraan dan klik <strong>Hapus</strong>. Kendaraan yang masih memiliki pengajuan aktif tidak dapat dihapus — selesaikan semua perjalanan aktif terkait kendaraan tersebut terlebih dahulu.',
                    ],
                    [
                        'q' => 'Mengapa kendaraan yang saya tambahkan tidak muncul di form pengajuan?',
                        'a' => 'Pastikan status kendaraan diatur ke <strong>aktif</strong>. Kendaraan dengan status tidak aktif secara otomatis disembunyikan dari daftar pilihan pada form pengajuan.',
                    ],
                ],
            ],

            [
                'id'    => 'laporan-admin',
                'title' => 'Laporan',
                'icon'  => 'chart',
                'color' => 'gray',
                'roles' => $admin,
                'items' => [
                    [
                        'q' => 'Bagaimana cara melihat riwayat semua perjalanan?',
                        'a' => 'Buka menu <strong>Laporan</strong>. Di sana tersedia riwayat seluruh perjalanan dari semua pengguna. Gunakan filter tanggal, kendaraan, atau nama pengguna untuk mempersempit hasil.',
                    ],
                    [
                        'q' => 'Bagaimana cara mengekspor laporan perjalanan?',
                        'a' => 'Di halaman <strong>Laporan</strong>, atur filter yang diinginkan lalu klik tombol <strong>Ekspor Excel</strong>. File Excel berisi detail semua perjalanan sesuai filter yang dipilih akan langsung diunduh.',
                    ],
                    [
                        'q' => 'Bagaimana cara membatalkan pengajuan yang sudah disetujui?',
                        'a' => 'Admin GA dapat membatalkan pengajuan dari halaman Persiapan atau Pantau. Buka detail pengajuan, klik <strong>Batalkan</strong>, isi alasan pembatalan. Sistem otomatis mengirim notifikasi ke pemohon.',
                    ],
                ],
            ],

        ];

        return array_values(
            array_filter($sections, fn ($s) => in_array($role, $s['roles']))
        );
    }
}
