<?php

return [
    'navigation' => [
        'title' => 'Свой Поля',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'name'              => 'Название',
                    'code'              => 'код',
                    'code-helper-text'  => 'Код нужно начать с letter или underscore, и можно только contain letters, numbers, и underscores.',
                ],
            ],

            'options' => [
                'title' => 'Options',

                'fields' => [
                    'add-option' => 'Добавить Option',
                ],
            ],

            'form-settings' => [
                'title' => 'Форма Настройки',

                'field-sets' => [
                    'validations' => [
                        'title' => 'Validations',

                        'fields' => [
                            'validation'     => 'Validation',
                            'field'          => 'Поле',
                            'value'          => 'Значение',
                            'add-validation' => 'Добавить Validation',
                        ],
                    ],

                    'additional-settings' => [
                        'title' => 'Additional Настройки',

                        'fields' => [
                            'setting'     => 'Настройка',
                            'value'       => 'Значение',
                            'color'       => 'Цвет',
                            'add-setting' => 'Добавить Настройка',

                            'color-options' => [
                                'danger'    => 'Danger',
                                'info'      => 'Info',
                                'primary'   => 'Primary',
                                'secondary' => 'Secondary',
                                'warning'   => 'Предупреждение',
                                'success'   => 'Успешно',
                            ],

                            'grid-options' => [
                                'row'    => 'Строка',
                                'column' => 'Колонка',
                            ],

                            'input-modes' => [
                                'text'     => 'Text',
                                'email'    => 'Эл. почта',
                                'numeric'  => 'Numeric',
                                'integer'  => 'Integer',
                                'password' => 'Пароль',
                                'tel'      => 'Telephone',
                                'url'      => 'URL',
                                'color'    => 'Цвет',
                                'none'     => 'Нет',
                                'decimal'  => 'Decimal',
                                'search'   => 'Поиск',
                                'url'      => 'URL',
                            ],
                        ],
                    ],
                ],

                'validations' => [
                    'common' => [
                        'gt'                   => 'Greater Чем',
                        'gte'                  => 'Greater Чем или Equal',
                        'lt'                   => 'Меньше Чем',
                        'lte'                  => 'Меньше Чем или Equal',
                        'max-size'             => 'Max Размер',
                        'min-size'             => 'Min Размер',
                        'multiple-of'          => 'Multiple',
                        'nullable'             => 'Nullable',
                        'prohibited'           => 'Prohibited',
                        'prohibited-if'        => 'Prohibited Если',
                        'prohibited-unless'    => 'Prohibited Unless',
                        'prohibits'            => 'Prohibits',
                        'required'             => 'Обязательно',
                        'required-if'          => 'Обязательно Если',
                        'required-if-accepted' => 'Обязательно Если Accepted',
                        'required-unless'      => 'Обязательно Unless',
                        'required-with'        => 'Обязательно С',
                        'required-with-all'    => 'Обязательно С Все',
                        'required-without'     => 'Обязательно Без',
                        'required-without-all' => 'Обязательно Без Все',
                        'rules'                => 'Свой Rules',
                        'unique'               => 'Unique',
                    ],

                    'text' => [
                        'alpha-dash'        => 'Alpha Dash',
                        'alpha-num'         => 'Alpha Numeric',
                        'ascii'             => 'ASCII',
                        'doesnt-end-with'   => "Doesn't Конец С",
                        'doesnt-start-with' => "Doesn't Начать С",
                        'ends-with'         => 'Ends С',
                        'filled'            => 'Filled',
                        'ip'                => 'IP',
                        'ipv4'              => 'IPv4',
                        'ipv6'              => 'IPv6',
                        'length'            => 'Length',
                        'mac-address'       => 'MAC Адрес',
                        'max-length'        => 'Max Length',
                        'min-length'        => 'Min Length',
                        'regex'             => 'Regex',
                        'starts-with'       => 'Starts С',
                        'ulid'              => 'ULID',
                        'uuid'              => 'UUID',
                    ],

                    'textarea' => [
                        'filled'     => 'Filled',
                        'max-length' => 'Max Length',
                        'min-length' => 'Min Length',
                    ],

                    'select' => [
                        'different'  => 'Different',
                        'exists'     => 'Существует',
                        'in'         => 'В',
                        'not-in'     => 'Не В',
                        'same'       => 'Тот же',
                    ],

                    'radio' => [],

                    'checkbox' => [
                        'accepted' => 'Accepted',
                        'declined' => 'Declined',
                    ],

                    'toggle' => [
                        'accepted' => 'Accepted',
                        'declined' => 'Declined',
                    ],

                    'checkbox-list' => [
                        'in'        => 'В',
                        'max-items' => 'Max Позиции',
                        'min-items' => 'Min Позиции',
                    ],

                    'datetime' => [
                        'after'           => 'После',
                        'after-or-equal'  => 'После или Equal',
                        'before'          => 'До',
                        'before-or-equal' => 'До или Equal',
                    ],

                    'editor' => [
                        'filled'     => 'Filled',
                        'max-length' => 'Max Length',
                        'min-length' => 'Min Length',
                    ],

                    'markdown' => [
                        'filled'     => 'Filled',
                        'max-length' => 'Max Length',
                        'min-length' => 'Min Length',
                    ],

                    'color' => [
                        'hex-color' => 'Hex Цвет',
                    ],
                ],

                'settings' => [
                    'text' => [
                        'autocapitalize'    => 'Autocapitalize',
                        'autocomplete'      => 'Autocomplete',
                        'autofocus'         => 'Autofocus',
                        'default'           => 'По умолчанию Значение',
                        'disabled'          => 'Выключено',
                        'helper-text'       => 'Helper Text',
                        'hint'              => 'Hint',
                        'hint-color'        => 'Hint Цвет',
                        'hint-icon'         => 'Hint Значок',
                        'id'                => 'Ид.',
                        'input-mode'        => 'Input Mode',
                        'mask'              => 'Mask',
                        'placeholder'       => 'Placeholder',
                        'prefix'            => 'Префикс',
                        'prefix-icon'       => 'Префикс Значок',
                        'prefix-icon-color' => 'Префикс Значок Цвет',
                        'read-only'         => 'Чтение Только',
                        'step'              => 'Step',
                        'suffix'            => 'Суффикс',
                        'suffix-icon'       => 'Суффикс Значок',
                        'suffix-icon-color' => 'Суффикс Значок Цвет',
                    ],

                    'textarea' => [
                        'autofocus'    => 'Autofocus',
                        'autosize'     => 'Autosize',
                        'cols'         => 'Колонки',
                        'default'      => 'По умолчанию Значение',
                        'disabled'     => 'Выключено',
                        'helperText'   => 'Helper Text',
                        'hint'         => 'Hint',
                        'hintColor'    => 'Hint Цвет',
                        'hintIcon'     => 'Hint Значок',
                        'id'           => 'Ид.',
                        'placeholder'  => 'Placeholder',
                        'read-only'    => 'Чтение Только',
                        'rows'         => 'Строки',
                    ],

                    'select' => [
                        'default'                   => 'По умолчанию Значение',
                        'disabled'                  => 'Выключено',
                        'helper-text'               => 'Helper Text',
                        'hint'                      => 'Hint',
                        'hint-color'                => 'Hint Цвет',
                        'hint-icon'                 => 'Hint Значок',
                        'id'                        => 'Ид.',
                        'loading-message'           => 'Загрузка Сообщение',
                        'no-search-results-message' => 'Нет Поиск Результаты Сообщение',
                        'options-limit'             => 'Options Limit',
                        'preload'                   => 'Preload',
                        'searchable'                => 'Searchable',
                        'search-debounce'           => 'Поиск Debounce',
                        'searching-message'         => 'Searching Сообщение',
                        'search-prompt'             => 'Поиск Prompt',
                    ],

                    'radio' => [
                        'default'     => 'По умолчанию Значение',
                        'disabled'    => 'Выключено',
                        'helper-text' => 'Helper Text',
                        'hint'        => 'Hint',
                        'hint-color'  => 'Hint Цвет',
                        'hint-icon'   => 'Hint Значок',
                        'id'          => 'Ид.',
                    ],

                    'checkbox' => [
                        'default'     => 'По умолчанию Значение',
                        'disabled'    => 'Выключено',
                        'helper-text' => 'Helper Text',
                        'hint'        => 'Hint',
                        'hint-color'  => 'Hint Цвет',
                        'hint-icon'   => 'Hint Значок',
                        'id'          => 'Ид.',
                        'inline'      => 'Inline',
                    ],

                    'toggle' => [
                        'default'     => 'По умолчанию Значение',
                        'disabled'    => 'Выключено',
                        'helper-text' => 'Helper Text',
                        'hint'        => 'Hint',
                        'hint-color'  => 'Hint Цвет',
                        'hint-icon'   => 'Hint Значок',
                        'id'          => 'Ид.',
                        'off-color'   => 'Выкл. Цвет',
                        'off-icon'    => 'Выкл. Значок',
                        'on-color'    => 'Вкл. Цвет',
                        'on-icon'     => 'Вкл. Значок',
                    ],

                    'checkbox-list' => [
                        'bulk-toggleable'           => 'Bulk Toggleable',
                        'columns'                   => 'Колонки',
                        'default'                   => 'По умолчанию Значение',
                        'disabled'                  => 'Выключено',
                        'grid-direction'            => 'Grid Direction',
                        'helper-text'               => 'Helper Text',
                        'hint'                      => 'Hint',
                        'hint-color'                => 'Hint Цвет',
                        'hint-icon'                 => 'Hint Значок',
                        'id'                        => 'Ид.',
                        'max-items'                 => 'Max Позиции',
                        'min-items'                 => 'Min Позиции',
                        'no-search-results-message' => 'Нет Поиск Результаты Сообщение',
                        'searchable'                => 'Searchable',
                    ],

                    'datetime' => [
                        'close-on-date-selection' => 'Закрыть вкл. Дата Selection',
                        'default'                 => 'По умолчанию Значение',
                        'disabled'                => 'Выключено',
                        'disabled-dates'          => 'Выключено Dates',
                        'display-format'          => 'Display Формат',
                        'first-day-of-week'       => 'Первый День Неделя',
                        'format'                  => 'Формат',
                        'helper-text'             => 'Helper Text',
                        'hint'                    => 'Hint',
                        'hint-color'              => 'Hint Цвет',
                        'hint-icon'               => 'Hint Значок',
                        'hours-step'              => 'Часы Step',
                        'id'                      => 'Ид.',
                        'locale'                  => 'Locale',
                        'minutes-step'            => 'Минуты Step',
                        'seconds'                 => 'Seconds',
                        'seconds-step'            => 'Seconds Step',
                        'timezone'                => 'Timezone',
                        'week-starts-on-monday'   => 'Неделя Starts вкл. Понедельник',
                        'week-starts-on-sunday'   => 'Неделя Starts вкл. Воскресенье',
                    ],

                    'editor' => [
                        'default'      => 'По умолчанию Значение',
                        'disabled'     => 'Выключено',
                        'helper-text'  => 'Helper Text',
                        'hint'         => 'Hint',
                        'hint-color'   => 'Hint Цвет',
                        'hint-icon'    => 'Hint Значок',
                        'id'           => 'Ид.',
                        'placeholder'  => 'Placeholder',
                        'read-only'    => 'Чтение Только',
                    ],

                    'markdown' => [
                        'default'      => 'По умолчанию Значение',
                        'disabled'     => 'Выключено',
                        'helper-text'  => 'Helper Text',
                        'hint'         => 'Hint',
                        'hint-color'   => 'Hint Цвет',
                        'hint-icon'    => 'Hint Значок',
                        'id'           => 'Ид.',
                        'placeholder'  => 'Placeholder',
                        'read-only'    => 'Чтение Только',
                    ],

                    'color' => [
                        'default'     => 'По умолчанию Значение',
                        'disabled'    => 'Выключено',
                        'helper-text' => 'Helper Text',
                        'hint'        => 'Hint',
                        'hint-color'  => 'Hint Цвет',
                        'hint-icon'   => 'Hint Значок',
                        'hsl'         => 'HSL',
                        'id'          => 'Ид.',
                        'rgb'         => 'RGB',
                        'rgba'        => 'RGBA',
                    ],

                    'file' => [
                        'accepted-file-types'                   => 'Accepted Файл Types',
                        'append-files'                          => 'Append Файлы',
                        'deletable'                             => 'Deletable',
                        'directory'                             => 'Directory',
                        'downloadable'                          => 'Downloadable',
                        'fetch-file-information'                => 'Fetch Файл Сведения',
                        'file-attachments-directory'            => 'Файл Вложения Directory',
                        'file-attachments-visibility'           => 'Файл Вложения Visibility',
                        'image'                                 => 'Изображение',
                        'image-crop-aspect-ratio'               => 'Изображение Crop Aspect Ratio',
                        'image-editor'                          => 'Изображение Editor',
                        'image-editor-aspect-ratios'            => 'Изображение Editor Aspect Ratios',
                        'image-editor-empty-fill-color'         => 'Изображение Editor Пусто Fill Цвет',
                        'image-editor-mode'                     => 'Изображение Editor Mode',
                        'image-preview-height'                  => 'Изображение Preview Высота',
                        'image-resize-mode'                     => 'Изображение Resize Mode',
                        'image-resize-target-height'            => 'Изображение Resize Назначение Высота',
                        'image-resize-target-width'             => 'Изображение Resize Назначение Ширина',
                        'loading-indicator-position'            => 'Загрузка Indicator Должность',
                        'move-files'                            => 'Переместить Файлы',
                        'openable'                              => 'Openable',
                        'orient-images-from-exif'               => 'Orient Images от EXIF',
                        'panel-aspect-ratio'                    => 'Panel Aspect Ratio',
                        'panel-layout'                          => 'Panel Layout',
                        'previewable'                           => 'Previewable',
                        'remove-uploaded-file-button-position'  => 'Убрать Uploaded Файл Button Должность',
                        'reorderable'                           => 'Reorderable',
                        'store-files'                           => 'Store Файлы',
                        'upload-button-position'                => 'Загрузить Button Должность',
                        'uploading-message'                     => 'Uploading Сообщение',
                        'upload-progress-indicator-position'    => 'Загрузить Ход Indicator Должность',
                        'visibility'                            => 'Visibility',
                    ],
                ],
            ],

            'table-settings' => [
                'title' => 'Таблица Настройки',

                'fields' => [
                    'use-in-table'  => 'Использовать в Таблица',
                    'setting'       => 'Настройка',
                    'value'         => 'Значение',
                    'color'         => 'Цвет',
                    'alignment'     => 'Alignment',
                    'font-weight'   => 'Font Вес',
                    'icon-position' => 'Значок Должность',
                    'size'          => 'Размер',
                    'add-setting'   => 'Добавить Настройка',

                    'color-options' => [
                        'danger'    => 'Danger',
                        'info'      => 'Info',
                        'primary'   => 'Primary',
                        'secondary' => 'Secondary',
                        'warning'   => 'Предупреждение',
                        'success'   => 'Успешно',
                    ],

                    'alignment-options' => [
                        'start'   => 'Начать',
                        'left'    => 'Осталось',
                        'center'  => 'Center',
                        'end'     => 'Конец',
                        'right'   => 'Справа',
                        'justify' => 'Justify',
                        'between' => 'Между',
                    ],

                    'font-weight-options' => [
                        'thin'        => 'Thin',
                        'extra-light' => 'Extra Светлая',
                        'light'       => 'Светлая',
                        'normal'      => 'Обычный',
                        'medium'      => 'Средний',
                        'semi-bold'   => 'Semi Bold',
                        'bold'        => 'Bold',
                        'extra-bold'  => 'Extra Bold',
                        'black'       => 'Black',
                    ],

                    'icon-position-options' => [
                        'before'  => 'До',
                        'after'   => 'После',
                    ],

                    'size-options' => [
                        'extra-small' => 'Extra Малый',
                        'small'       => 'Малый',
                        'medium'      => 'Средний',
                        'large'       => 'Большой',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'Align Конец',
                        'alignment'              => 'Alignment',
                        'align-start'            => 'Align Начать',
                        'badge'                  => 'Badge',
                        'boolean'                => 'Boolean',
                        'color'                  => 'Цвет',
                        'copyable'               => 'Copyable',
                        'copy-message'           => 'Копировать Сообщение',
                        'copy-message-duration'  => 'Копировать Сообщение Duration',
                        'default'                => 'По умолчанию',
                        'filterable'             => 'Filterable',
                        'groupable'              => 'Groupable',
                        'grow'                   => 'Grow',
                        'icon'                   => 'Значок',
                        'icon-color'             => 'Значок Цвет',
                        'icon-position'          => 'Значок Должность',
                        'label'                  => 'Подпись',
                        'limit'                  => 'Limit',
                        'line-clamp'             => 'Строка Clamp',
                        'money'                  => 'Money',
                        'placeholder'            => 'Placeholder',
                        'prefix'                 => 'Префикс',
                        'searchable'             => 'Searchable',
                        'size'                   => 'Размер',
                        'sortable'               => 'Sortable',
                        'suffix'                 => 'Суффикс',
                        'toggleable'             => 'Toggleable',
                        'tooltip'                => 'Tooltip',
                        'vertical-alignment'     => 'Vertical Alignment',
                        'vertically-align-start' => 'Vertically Align Начать',
                        'weight'                 => 'Вес',
                        'width'                  => 'Ширина',
                        'words'                  => 'Words',
                        'wrap-header'            => 'Wrap Header',
                        'column-span'            => 'Колонка Span',
                        'helper-text'            => 'Helper Text',
                        'hint'                   => 'Hint',
                        'hint-color'             => 'Hint Цвет',
                        'hint-icon'              => 'Hint Значок',
                    ],

                    'datetime' => [
                        'date'              => 'Дата',
                        'date-time'         => 'Дата Time',
                        'date-time-tooltip' => 'Дата Time Tooltip',
                        'since'             => 'Since',
                    ],
                ],
            ],

            'infolist-settings' => [
                'title' => 'Infolist Настройки',

                'fields' => [
                    'setting'       => 'Настройка',
                    'value'         => 'Значение',
                    'color'         => 'Цвет',
                    'font-weight'   => 'Font Вес',
                    'icon-position' => 'Значок Должность',
                    'size'          => 'Размер',
                    'add-setting'   => 'Добавить Настройка',

                    'color-options' => [
                        'danger'    => 'Danger',
                        'info'      => 'Info',
                        'primary'   => 'Primary',
                        'secondary' => 'Secondary',
                        'warning'   => 'Предупреждение',
                        'success'   => 'Успешно',
                    ],

                    'font-weight-options' => [
                        'thin'        => 'Thin',
                        'extra-light' => 'Extra Светлая',
                        'light'       => 'Светлая',
                        'normal'      => 'Обычный',
                        'medium'      => 'Средний',
                        'semi-bold'   => 'Semi Bold',
                        'bold'        => 'Bold',
                        'extra-bold'  => 'Extra Bold',
                        'black'       => 'Black',
                    ],

                    'icon-position-options' => [
                        'before'  => 'До',
                        'after'   => 'После',
                    ],

                    'size-options' => [
                        'extra-small' => 'Extra Малый',
                        'small'       => 'Малый',
                        'medium'      => 'Средний',
                        'large'       => 'Большой',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'Align Конец',
                        'alignment'              => 'Alignment',
                        'align-start'            => 'Align Начать',
                        'badge'                  => 'Badge',
                        'boolean'                => 'Boolean',
                        'color'                  => 'Цвет',
                        'copyable'               => 'Copyable',
                        'copy-message'           => 'Копировать Сообщение',
                        'copy-message-duration'  => 'Копировать Сообщение Duration',
                        'default'                => 'По умолчанию',
                        'filterable'             => 'Filterable',
                        'groupable'              => 'Groupable',
                        'grow'                   => 'Grow',
                        'icon'                   => 'Значок',
                        'icon-color'             => 'Значок Цвет',
                        'icon-position'          => 'Значок Должность',
                        'label'                  => 'Подпись',
                        'limit'                  => 'Limit',
                        'line-clamp'             => 'Строка Clamp',
                        'money'                  => 'Money',
                        'placeholder'            => 'Placeholder',
                        'prefix'                 => 'Префикс',
                        'searchable'             => 'Searchable',
                        'size'                   => 'Размер',
                        'sortable'               => 'Sortable',
                        'suffix'                 => 'Суффикс',
                        'toggleable'             => 'Toggleable',
                        'tooltip'                => 'Tooltip',
                        'vertical-alignment'     => 'Vertical Alignment',
                        'vertically-align-start' => 'Vertically Align Начать',
                        'weight'                 => 'Вес',
                        'width'                  => 'Ширина',
                        'words'                  => 'Words',
                        'wrap-header'            => 'Wrap Header',
                        'column-span'            => 'Колонка Span',
                        'helper-text'            => 'Helper Text',
                        'hint'                   => 'Hint',
                        'hint-color'             => 'Hint Цвет',
                        'hint-icon'              => 'Hint Значок',
                    ],

                    'datetime' => [
                        'date'              => 'Дата',
                        'date-time'         => 'Дата Time',
                        'date-time-tooltip' => 'Дата Time Tooltip',
                        'since'             => 'Since',
                    ],

                    'checkbox-list' => [
                        'separator'                => 'Separator',
                        'list-with-line-breaks'    => 'Список с Строка Breaks',
                        'bulleted'                 => 'Bulleted',
                        'limit-list'               => 'Limit Список',
                        'expandable-limited-list'  => 'Expandable Limited Список',
                    ],

                    'select' => [
                        'separator'                => 'Separator',
                        'list-with-line-breaks'    => 'Список с Строка Breaks',
                        'bulleted'                 => 'Bulleted',
                        'limit-list'               => 'Limit Список',
                        'expandable-limited-list'  => 'Expandable Limited Список',
                    ],

                    'checkbox' => [
                        'boolean'     => 'Boolean',
                        'false-icon'  => 'Нет Значок',
                        'true-icon'   => 'Да Значок',
                        'true-color'  => 'Да Цвет',
                        'false-color' => 'Нет Цвет',
                    ],

                    'toggle' => [
                        'boolean'     => 'Boolean',
                        'false-icon'  => 'Нет Значок',
                        'true-icon'   => 'Да Значок',
                        'true-color'  => 'Да Цвет',
                        'false-color' => 'Нет Цвет',
                    ],
                ],
            ],

            'settings' => [
                'title' => 'Настройки',

                'fields' => [
                    'type'           => 'Тип',
                    'input-type'     => 'Input Тип',
                    'is-multiselect' => 'Multiselect',
                    'sort-order'     => 'Sort Заказ',

                    'type-options' => [
                        'text'          => 'Text Input',
                        'textarea'      => 'Textarea',
                        'select'        => 'Выбрать',
                        'checkbox'      => 'Checkbox',
                        'radio'         => 'Radio',
                        'toggle'        => 'Toggle',
                        'checkbox-list' => 'Checkbox Список',
                        'datetime'      => 'Дата Time Picker',
                        'editor'        => 'Rich Text Editor',
                        'markdown'      => 'Markdown Editor',
                        'color'         => 'Цвет Picker',
                    ],

                    'input-type-options' => [
                        'text'     => 'Text',
                        'email'    => 'Эл. почта',
                        'numeric'  => 'Numeric',
                        'integer'  => 'Integer',
                        'password' => 'Пароль',
                        'tel'      => 'Telephone',
                        'url'      => 'URL',
                        'color'    => 'Цвет',
                    ],
                ],
            ],

            'resource' => [
                'title' => 'Resource',

                'fields' => [
                    'plugin'   => 'Модуль',
                    'resource' => 'Resource',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'       => 'Код',
            'name'       => 'Название',
            'type'       => 'Тип',
            'resource'   => 'Resource',
            'created-at' => 'Создано',
        ],

        'groups' => [
        ],

        'filters' => [
            'type'     => 'Тип',
            'resource' => 'Resource',

            'type-options' => [
                'text'          => 'Text Input',
                'textarea'      => 'Textarea',
                'select'        => 'Выбрать',
                'checkbox'      => 'Checkbox',
                'radio'         => 'Radio',
                'toggle'        => 'Toggle',
                'checkbox-list' => 'Checkbox Список',
                'datetime'      => 'Дата Time Picker',
                'editor'        => 'Rich Text Editor',
                'markdown'      => 'Markdown Editor',
                'color'         => 'Цвет Picker',
            ],
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Поле restored',
                    'body'  => 'поле есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Поле удалено',
                    'body'  => 'поле есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Поле force удалено',
                    'body'  => 'поле есть force удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Поля restored',
                    'body'  => 'поля есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Поля удалено',
                    'body'  => 'поля есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Поля force удалено',
                    'body'  => 'поля есть force удалено successfully.',
                ],
            ],
        ],
    ],
];
