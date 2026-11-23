<?php

return [

    'uninstall' => [
        'title'   => 'Uninstall Confirmation',
        'message' => 'Ni wewe sure wewe want hadi uninstall :name programu-jalizi?',
        'warning' => '⚠️ Hii kitendo haiwezekani kuwa undone na ita permanently futa data.',
    ],

    'dependents' => [
        'title'         => 'Dependent Programu-jalizi',
        'description'   => 'Hizi programu-jalizi depend washa hii one. Yoyote installed dependents lazima kuwa uninstalled kwanza.',
        'installed'     => 'Installed',
        'not_installed' => 'Si Installed',
    ],

    'dependency_warning' => [
        'title'   => 'Kitendo Inahitajika',
        'message' => '⚠️ Tafadhali uninstall ifuatayo dependent programu-jalizi kwanza kabla ya uninstalling :name.',
    ],

    'data_impact' => [
        'title'       => 'Data Impact',
        'description' => 'ifuatayo database tables contain data hiyo ita kuwa permanently imefutwa.',
        'records'     => ':count rekodi',
    ],

];
