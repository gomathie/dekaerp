<?php

/**
 * This plugin contributes no Filament resources or admin pages any more.
 *
 * It listed three before 2026-10-22 - the admin and customer PageResources and
 * a duplicate PartnerResource - all of which went with the CMS. What remains is
 * the customer portal: a cluster and auth pages on the `customer` panel, plus
 * the portal-access actions that PortalContributions injects into the Partners
 * plugin's own resource. Those actions are governed by the Partner resource's
 * permissions, not by anything declared here.
 *
 * Kept as an empty declaration rather than deleted so the plugin's Shield
 * contribution stays explicit, and so adding one later has an obvious home.
 */
return [
    'resources' => [
        'manage'  => [],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [],
    ],
];
