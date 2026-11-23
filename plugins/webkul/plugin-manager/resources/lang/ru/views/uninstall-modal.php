<?php

return [

    'uninstall' => [
        'title'   => 'Uninstall Confirmation',
        'message' => 'вы sure вы want до uninstall :name модуль?',
        'warning' => '⚠️ Этот действие нельзя быть undone и permanently удалить data.',
    ],

    'dependents' => [
        'title'         => 'Dependent Модули',
        'description'   => 'Эти модули depend вкл. этот one. Любой installed dependents нужно быть uninstalled первый.',
        'installed'     => 'Installed',
        'not_installed' => 'Не Installed',
    ],

    'dependency_warning' => [
        'title'   => 'Действие Обязательно',
        'message' => '⚠️ Пожалуйста uninstall следующий dependent модули первый до uninstalling :name.',
    ],

    'data_impact' => [
        'title'       => 'Data Impact',
        'description' => 'следующий database tables contain data тот быть permanently удалено.',
        'records'     => ':count записи',
    ],

];
