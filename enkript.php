use phpseclib\Crypt\PGP;

public function encryptPdf($userId, $pdfPath)
{
    $user = User::findOrFail($userId);
    if (!$user->pgp_public_key) {
        return response()->json(['error' => 'User does not have a PGP key'], 400);
    }

    $pdfContent = file_get_contents($pdfPath);
    $pgp = new PGP();
    $encryptedPdf = $pgp->encrypt($pdfContent, $user->pgp_public_key);

    file_put_contents(storage_path('app/public/encrypted_file_for_user_'.$userId.'.pdf.gpg'), $encryptedPdf);
    return response()->download(storage_path('app/public/encrypted_file_for_user_'.$userId.'.pdf.gpg'));
}
