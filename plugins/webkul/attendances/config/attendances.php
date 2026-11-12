<?php

return [
    /*
     * Uninstall refuses while attendance rows exist, so a company cannot lose
     * its clock history by removing the plugin. Tests and a deliberate wipe
     * set this true.
     */
    'allow_uninstall_with_data' => false,
];
