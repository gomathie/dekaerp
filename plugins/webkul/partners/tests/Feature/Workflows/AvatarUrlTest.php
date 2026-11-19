<?php

use Webkul\Partner\Models\Partner;

/**
 * 2026-11-19: on tenant-s3 a contact's picture link was built from the
 * *active* company, so a Super Admin who switched company got a 404 for
 * their own picture. The link now names the contact's own company folder.
 */
it('links a contact picture to its own company folder on tenant storage', function () {
    config([
        'filesystems.disks.public.driver' => 'tenant-s3',
        'filesystems.disks.public.root'   => '',
        'filesystems.disks.public.url'    => 'https://erp.test/secure-storage',
    ]);

    $partner = new Partner(['avatar' => 'users/avatars/me.png']);
    $partner->company_id = 7;

    expect($partner->avatar_url)->toBe('https://erp.test/secure-storage/companies/7/users/avatars/me.png');
});

it('keeps the plain disk link on local storage', function () {
    config(['filesystems.disks.public.driver' => 'local']);

    $partner = new Partner(['avatar' => 'users/avatars/me.png']);
    $partner->company_id = 7;

    expect($partner->avatar_url)->not->toContain('companies/7');
});
