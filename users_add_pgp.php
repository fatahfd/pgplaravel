Schema::table('users', function (Blueprint $table) {
    $table->text('pgp_public_key')->nullable();
    $table->text('pgp_private_key')->nullable();
});
