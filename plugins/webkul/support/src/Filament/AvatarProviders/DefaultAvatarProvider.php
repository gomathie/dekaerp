<?php

namespace Webkul\Support\Filament\AvatarProviders;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Database\Eloquent\Model;

/**
 * The placeholder shown for anyone without an uploaded avatar.
 *
 * Filament's own provider is UiAvatarsProvider, which builds an initials image
 * by requesting ui-avatars.com with the person's **name in the URL**. On a users
 * table that is one request per row, so a page of fifty sends fifty employee
 * names of this application's customers to a third party - and the images fail
 * on a restricted or offline network.
 *
 * This serves one local asset instead. Everyone without a picture looks the
 * same, which was the accepted trade (user, 2026-09-24).
 */
class DefaultAvatarProvider implements AvatarProvider
{
    public function get(Model $record): string
    {
        return asset('images/default-avatar.svg');
    }
}
