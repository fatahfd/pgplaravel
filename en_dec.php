namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use phpseclib\Crypt\PGP;

class PdfEncryptionController extends Controller
{
    public function encryptPdf(Request $request, $userId)
    {
        // Ambil user berdasarkan ID
        $user = User::findOrFail($userId);

        // Cek apakah user memiliki kunci PGP
        if (!$user->pgp_public_key) {
            return response()->json(['error' => 'User does not have a PGP key'], 400);
        }

        // Path file PDF yang akan dienkripsi
        $pdfPath = storage_path('app/public/file.pdf');

        // Muat file PDF
        $pdfContent = file_get_contents($pdfPath);

        // Inisialisasi PGP
        $pgp = new PGP();

        // Enkripsi menggunakan kunci publik user
        $encryptedPdf = $pgp->encrypt($pdfContent, $user->pgp_public_key);

        // Simpan file PDF yang sudah terenkripsi
        file_put_contents(storage_path('app/public/encrypted_file_for_user_'.$userId.'.pdf.gpg'), $encryptedPdf);

        return response()->download(storage_path('app/public/encrypted_file_for_user_'.$userId.'.pdf.gpg'));
    }

    public function decryptPdf(Request $request, $userId)
    {
        // Ambil user berdasarkan ID
        $user = User::findOrFail($userId);

        // Cek apakah user memiliki kunci PGP
        if (!$user->pgp_private_key) {
            return response()->json(['error' => 'User does not have a PGP key'], 400);
        }

        // Path file PDF yang terenkripsi
        $encryptedPdfPath = storage_path('app/public/encrypted_file_for_user_'.$userId.'.pdf.gpg');

        // Muat file PDF yang terenkripsi
        $encryptedPdf = file_get_contents($encryptedPdfPath);

        // Inisialisasi PGP
        $pgp = new PGP();

        // Dekripsi menggunakan kunci privat user
        $decryptedPdf = $pgp->decrypt($encryptedPdf, $user->pgp_private_key, 'your_passphrase'); // Gunakan passphrase jika diperlukan

        // Simpan file PDF yang sudah didekripsi
        file_put_contents(storage_path('app/public/decrypted_file_for_user_'.$userId.'.pdf'), $decryptedPdf);

        return response()->download(storage_path('app/public/decrypted_file_for_user_'.$userId.'.pdf'));
    }
}
