use phpseclib\Crypt\PGP;

public function generatePgpKeys()
{
    $pgp = new PGP();
    $keys = $pgp->generateKeyPair([
        'name' => $this->name,
        'email' => $this->email,
        'passphrase' => 'your_passphrase',
    ]);

    $this->pgp_public_key = $keys['publickey'];
    $this->pgp_private_key = $keys['privatekey'];
    $this->save();
}
