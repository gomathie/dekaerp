<?php

use Webkul\TableViews\Filament\Components\SavedView;
use Webkul\TableViews\Models\TableView;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

it('keeps saved view model access compatible with Filament components', function () {
    $tableView = new TableView;
    $savedView = SavedView::make('Saved filters')->model($tableView);

    expect($savedView->getModel())->toBe(TableView::class)
        ->and($savedView->getRecord())->toBe($tableView);
});
