# Dealer Car Market

## Staging installation

1. In Hostinger phpMyAdmin for `u465223560_usedcars_stg`, import `schema.sql`.
2. In the staging website folder, copy `config.example.php` to `config.local.php`, then enter the staging database password. `config.local.php` is ignored by Git and must never be committed.
3. Ensure the `uploads/` folder is writable by PHP (usually permission 755 on Hostinger is sufficient).
4. In `config.local.php`, replace `setup_admin_token` with a long random one-time setup code. Then open `https://staging.lead9.in/setup-admin.php` and create the first admin with the email `gdirectory99@gmail.com`. The page permanently disables itself after the first admin exists.

## Live installation

Create a separate database ending in `_prod`; never point live at the staging database. Create a different `config.local.php` on the live Hostinger site with its own credentials, then import the same schema there.

## Security

- Never commit `config.local.php`, database passwords, or production uploads.
- Back up the database before major live changes.
- Notifications are deliberately not implemented yet. Inquiries are stored with `notification_status=not_configured` ready for future email/WhatsApp integration.
