<?php

return [
    'navigation' => [
        'title' => 'Пользовательские поля',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'name'              => 'Название',
                    'code'              => 'Код',
                    'code-helper-text'  => 'Код должен начинаться с буквы или подчёркивания и может содержать только буквы, цифры и подчёркивания.',
                ],
            ],

            'options' => [
                'title' => 'Варианты',

                'fields' => [
                    'add-option' => 'Добавить вариант',
                ],
            ],

            'form-settings' => [
                'title' => 'Настройки формы',

                'field-sets' => [
                    'validations' => [
                        'title' => 'Проверки',

                        'fields' => [
                            'validation'     => 'Проверка',
                            'field'          => 'Поле',
                            'value'          => 'Значение',
                            'add-validation' => 'Добавить проверку',
                        ],
                    ],

                    'additional-settings' => [
                        'title' => 'Дополнительные настройки',

                        'fields' => [
                            'setting'     => 'Параметр',
                            'value'       => 'Значение',
                            'color'       => 'Цвет',
                            'add-setting' => 'Добавить параметр',

                            'color-options' => [
                                'danger'    => 'Опасность',
                                'info'      => 'Информация',
                                'primary'   => 'Основной',
                                'secondary' => 'Дополнительный',
                                'warning'   => 'Предупреждение',
                                'success'   => 'Успех',
                            ],

                            'grid-options' => [
                                'row'    => 'Строка',
                                'column' => 'Столбец',
                            ],

                            'input-modes' => [
                                'text'     => 'Текст',
                                'email'    => 'Эл. почта',
                                'numeric'  => 'Числовой',
                                'integer'  => 'Целое число',
                                'password' => 'Пароль',
                                'tel'      => 'Телефон',
                                'url'      => 'URL',
                                'color'    => 'Цвет',
                                'none'     => 'Нет',
                                'decimal'  => 'Десятичное',
                                'search'   => 'Поиск',
                                'url'      => 'URL',
                            ],
                        ],
                    ],
                ],

                'validations' => [
                    'common' => [
                        'gt'                   => 'Больше',
                        'gte'                  => 'Больше или равно',
                        'lt'                   => 'Меньше',
                        'lte'                  => 'Меньше или равно',
                        'max-size'             => 'Макс. размер',
                        'min-size'             => 'Мин. размер',
                        'multiple-of'          => 'Кратно',
                        'nullable'             => 'Может быть пустым',
                        'prohibited'           => 'Запрещено',
                        'prohibited-if'        => 'Запрещено, если',
                        'prohibited-unless'    => 'Запрещено, если не',
                        'prohibits'            => 'Исключает',
                        'required'             => 'Обязательно',
                        'required-if'          => 'Обязательно, если',
                        'required-if-accepted' => 'Обязательно, если принято',
                        'required-unless'      => 'Обязательно, если не',
                        'required-with'        => 'Обязательно вместе с',
                        'required-with-all'    => 'Обязательно вместе со всеми',
                        'required-without'     => 'Обязательно без',
                        'required-without-all' => 'Обязательно без всех',
                        'rules'                => 'Собственные правила',
                        'unique'               => 'Уникально',
                    ],

                    'text' => [
                        'alpha-dash'        => 'Буквы, цифры, дефисы',
                        'alpha-num'         => 'Буквы и цифры',
                        'ascii'             => 'ASCII',
                        'doesnt-end-with'   => 'Не заканчивается на',
                        'doesnt-start-with' => 'Не начинается с',
                        'ends-with'         => 'Заканчивается на',
                        'filled'            => 'Заполнено',
                        'ip'                => 'IP',
                        'ipv4'              => 'IPv4',
                        'ipv6'              => 'IPv6',
                        'length'            => 'Длина',
                        'mac-address'       => 'MAC-адрес',
                        'max-length'        => 'Макс. длина',
                        'min-length'        => 'Мин. длина',
                        'regex'             => 'Регулярное выражение',
                        'starts-with'       => 'Начинается с',
                        'ulid'              => 'ULID',
                        'uuid'              => 'UUID',
                    ],

                    'textarea' => [
                        'filled'     => 'Заполнено',
                        'max-length' => 'Макс. длина',
                        'min-length' => 'Мин. длина',
                    ],

                    'select' => [
                        'different'  => 'Отличается от',
                        'exists'     => 'Существует',
                        'in'         => 'Входит в',
                        'not-in'     => 'Не входит в',
                        'same'       => 'Совпадает с',
                    ],

                    'radio' => [],

                    'checkbox' => [
                        'accepted' => 'Принято',
                        'declined' => 'Отклонено',
                    ],

                    'toggle' => [
                        'accepted' => 'Принято',
                        'declined' => 'Отклонено',
                    ],

                    'checkbox-list' => [
                        'in'        => 'Входит в',
                        'max-items' => 'Макс. элементов',
                        'min-items' => 'Мин. элементов',
                    ],

                    'datetime' => [
                        'after'           => 'После',
                        'after-or-equal'  => 'После или равно',
                        'before'          => 'До',
                        'before-or-equal' => 'До или равно',
                    ],

                    'editor' => [
                        'filled'     => 'Заполнено',
                        'max-length' => 'Макс. длина',
                        'min-length' => 'Мин. длина',
                    ],

                    'markdown' => [
                        'filled'     => 'Заполнено',
                        'max-length' => 'Макс. длина',
                        'min-length' => 'Мин. длина',
                    ],

                    'color' => [
                        'hex-color' => 'HEX-цвет',
                    ],
                ],

                'settings' => [
                    'text' => [
                        'autocapitalize'    => 'Автозаглавные',
                        'autocomplete'      => 'Автозаполнение',
                        'autofocus'         => 'Автофокус',
                        'default'           => 'Значение по умолчанию',
                        'disabled'          => 'Отключено',
                        'helper-text'       => 'Пояснение',
                        'hint'              => 'Подсказка',
                        'hint-color'        => 'Цвет подсказки',
                        'hint-icon'         => 'Значок подсказки',
                        'id'                => 'ID',
                        'input-mode'        => 'Режим ввода',
                        'mask'              => 'Маска',
                        'placeholder'       => 'Заполнитель',
                        'prefix'            => 'Префикс',
                        'prefix-icon'       => 'Значок префикса',
                        'prefix-icon-color' => 'Цвет значка префикса',
                        'read-only'         => 'Только чтение',
                        'step'              => 'Шаг',
                        'suffix'            => 'Суффикс',
                        'suffix-icon'       => 'Значок суффикса',
                        'suffix-icon-color' => 'Цвет значка суффикса',
                    ],

                    'textarea' => [
                        'autofocus'    => 'Автофокус',
                        'autosize'     => 'Автоматическая высота',
                        'cols'         => 'Столбцы',
                        'default'      => 'Значение по умолчанию',
                        'disabled'     => 'Отключено',
                        'helperText'   => 'Пояснение',
                        'hint'         => 'Подсказка',
                        'hintColor'    => 'Цвет подсказки',
                        'hintIcon'     => 'Значок подсказки',
                        'id'           => 'ID',
                        'placeholder'  => 'Заполнитель',
                        'read-only'    => 'Только чтение',
                        'rows'         => 'Строки',
                    ],

                    'select' => [
                        'default'                   => 'Значение по умолчанию',
                        'disabled'                  => 'Отключено',
                        'helper-text'               => 'Пояснение',
                        'hint'                      => 'Подсказка',
                        'hint-color'                => 'Цвет подсказки',
                        'hint-icon'                 => 'Значок подсказки',
                        'id'                        => 'ID',
                        'loading-message'           => 'Сообщение при загрузке',
                        'no-search-results-message' => 'Сообщение «ничего не найдено»',
                        'options-limit'             => 'Лимит вариантов',
                        'preload'                   => 'Предзагрузка',
                        'searchable'                => 'С поиском',
                        'search-debounce'           => 'Задержка поиска',
                        'searching-message'         => 'Сообщение во время поиска',
                        'search-prompt'             => 'Подсказка поиска',
                    ],

                    'radio' => [
                        'default'     => 'Значение по умолчанию',
                        'disabled'    => 'Отключено',
                        'helper-text' => 'Пояснение',
                        'hint'        => 'Подсказка',
                        'hint-color'  => 'Цвет подсказки',
                        'hint-icon'   => 'Значок подсказки',
                        'id'          => 'ID',
                    ],

                    'checkbox' => [
                        'default'     => 'Значение по умолчанию',
                        'disabled'    => 'Отключено',
                        'helper-text' => 'Пояснение',
                        'hint'        => 'Подсказка',
                        'hint-color'  => 'Цвет подсказки',
                        'hint-icon'   => 'Значок подсказки',
                        'id'          => 'ID',
                        'inline'      => 'В одну строку',
                    ],

                    'toggle' => [
                        'default'     => 'Значение по умолчанию',
                        'disabled'    => 'Отключено',
                        'helper-text' => 'Пояснение',
                        'hint'        => 'Подсказка',
                        'hint-color'  => 'Цвет подсказки',
                        'hint-icon'   => 'Значок подсказки',
                        'id'          => 'ID',
                        'off-color'   => 'Цвет «выкл.»',
                        'off-icon'    => 'Значок «выкл.»',
                        'on-color'    => 'Цвет «вкл.»',
                        'on-icon'     => 'Значок «вкл.»',
                    ],

                    'checkbox-list' => [
                        'bulk-toggleable'           => 'Выбор всех сразу',
                        'columns'                   => 'Столбцы',
                        'default'                   => 'Значение по умолчанию',
                        'disabled'                  => 'Отключено',
                        'grid-direction'            => 'Направление сетки',
                        'helper-text'               => 'Пояснение',
                        'hint'                      => 'Подсказка',
                        'hint-color'                => 'Цвет подсказки',
                        'hint-icon'                 => 'Значок подсказки',
                        'id'                        => 'ID',
                        'max-items'                 => 'Макс. элементов',
                        'min-items'                 => 'Мин. элементов',
                        'no-search-results-message' => 'Сообщение «ничего не найдено»',
                        'searchable'                => 'С поиском',
                    ],

                    'datetime' => [
                        'close-on-date-selection' => 'Закрывать при выборе даты',
                        'default'                 => 'Значение по умолчанию',
                        'disabled'                => 'Отключено',
                        'disabled-dates'          => 'Недоступные даты',
                        'display-format'          => 'Формат отображения',
                        'first-day-of-week'       => 'Первый день недели',
                        'format'                  => 'Формат',
                        'helper-text'             => 'Пояснение',
                        'hint'                    => 'Подсказка',
                        'hint-color'              => 'Цвет подсказки',
                        'hint-icon'               => 'Значок подсказки',
                        'hours-step'              => 'Шаг часов',
                        'id'                      => 'ID',
                        'locale'                  => 'Локаль',
                        'minutes-step'            => 'Шаг минут',
                        'seconds'                 => 'Секунды',
                        'seconds-step'            => 'Шаг секунд',
                        'timezone'                => 'Часовой пояс',
                        'week-starts-on-monday'   => 'Неделя с понедельника',
                        'week-starts-on-sunday'   => 'Неделя с воскресенья',
                    ],

                    'editor' => [
                        'default'      => 'Значение по умолчанию',
                        'disabled'     => 'Отключено',
                        'helper-text'  => 'Пояснение',
                        'hint'         => 'Подсказка',
                        'hint-color'   => 'Цвет подсказки',
                        'hint-icon'    => 'Значок подсказки',
                        'id'           => 'ID',
                        'placeholder'  => 'Заполнитель',
                        'read-only'    => 'Только чтение',
                    ],

                    'markdown' => [
                        'default'      => 'Значение по умолчанию',
                        'disabled'     => 'Отключено',
                        'helper-text'  => 'Пояснение',
                        'hint'         => 'Подсказка',
                        'hint-color'   => 'Цвет подсказки',
                        'hint-icon'    => 'Значок подсказки',
                        'id'           => 'ID',
                        'placeholder'  => 'Заполнитель',
                        'read-only'    => 'Только чтение',
                    ],

                    'color' => [
                        'default'     => 'Значение по умолчанию',
                        'disabled'    => 'Отключено',
                        'helper-text' => 'Пояснение',
                        'hint'        => 'Подсказка',
                        'hint-color'  => 'Цвет подсказки',
                        'hint-icon'   => 'Значок подсказки',
                        'hsl'         => 'HSL',
                        'id'          => 'ID',
                        'rgb'         => 'RGB',
                        'rgba'        => 'RGBA',
                    ],

                    'file' => [
                        'accepted-file-types'                   => 'Допустимые типы файлов',
                        'append-files'                          => 'Добавлять файлы в конец',
                        'deletable'                             => 'Можно удалять',
                        'directory'                             => 'Папка',
                        'downloadable'                          => 'Можно скачивать',
                        'fetch-file-information'                => 'Получать сведения о файле',
                        'file-attachments-directory'            => 'Папка вложений',
                        'file-attachments-visibility'           => 'Видимость вложений',
                        'image'                                 => 'Изображение',
                        'image-crop-aspect-ratio'               => 'Пропорции обрезки',
                        'image-editor'                          => 'Редактор изображений',
                        'image-editor-aspect-ratios'            => 'Пропорции в редакторе',
                        'image-editor-empty-fill-color'         => 'Цвет заливки пустой области',
                        'image-editor-mode'                     => 'Режим редактора',
                        'image-preview-height'                  => 'Высота превью',
                        'image-resize-mode'                     => 'Режим изменения размера',
                        'image-resize-target-height'            => 'Целевая высота изображения',
                        'image-resize-target-width'             => 'Целевая ширина изображения',
                        'loading-indicator-position'            => 'Положение индикатора загрузки',
                        'move-files'                            => 'Перемещать файлы',
                        'openable'                              => 'Можно открывать',
                        'orient-images-from-exif'               => 'Ориентация по EXIF',
                        'panel-aspect-ratio'                    => 'Пропорции панели',
                        'panel-layout'                          => 'Макет панели',
                        'previewable'                           => 'Предпросмотр',
                        'remove-uploaded-file-button-position'  => 'Положение кнопки удаления файла',
                        'reorderable'                           => 'Можно менять порядок',
                        'store-files'                           => 'Сохранять файлы',
                        'upload-button-position'                => 'Положение кнопки загрузки',
                        'uploading-message'                     => 'Сообщение при загрузке файла',
                        'upload-progress-indicator-position'    => 'Положение индикатора прогресса',
                        'visibility'                            => 'Видимость',
                    ],
                ],
            ],

            'table-settings' => [
                'title' => 'Настройки таблицы',

                'fields' => [
                    'use-in-table'  => 'Показывать в таблице',
                    'setting'       => 'Параметр',
                    'value'         => 'Значение',
                    'color'         => 'Цвет',
                    'alignment'     => 'Выравнивание',
                    'font-weight'   => 'Насыщенность шрифта',
                    'icon-position' => 'Положение значка',
                    'size'          => 'Размер',
                    'add-setting'   => 'Добавить параметр',

                    'color-options' => [
                        'danger'    => 'Опасность',
                        'info'      => 'Информация',
                        'primary'   => 'Основной',
                        'secondary' => 'Дополнительный',
                        'warning'   => 'Предупреждение',
                        'success'   => 'Успех',
                    ],

                    'alignment-options' => [
                        'start'   => 'Начало',
                        'left'    => 'Слева',
                        'center'  => 'По центру',
                        'end'     => 'Конец',
                        'right'   => 'Справа',
                        'justify' => 'По ширине',
                        'between' => 'Между',
                    ],

                    'font-weight-options' => [
                        'thin'        => 'Тонкий',
                        'extra-light' => 'Сверхсветлый',
                        'light'       => 'Светлый',
                        'normal'      => 'Обычный',
                        'medium'      => 'Средний',
                        'semi-bold'   => 'Полужирный',
                        'bold'        => 'Жирный',
                        'extra-bold'  => 'Сверхжирный',
                        'black'       => 'Чёрный',
                    ],

                    'icon-position-options' => [
                        'before'  => 'Перед',
                        'after'   => 'После',
                    ],

                    'size-options' => [
                        'extra-small' => 'Очень маленький',
                        'small'       => 'Маленький',
                        'medium'      => 'Средний',
                        'large'       => 'Большой',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'Выровнять по концу',
                        'alignment'              => 'Выравнивание',
                        'align-start'            => 'Выровнять по началу',
                        'badge'                  => 'Бейдж',
                        'boolean'                => 'Логическое значение',
                        'color'                  => 'Цвет',
                        'copyable'               => 'Можно копировать',
                        'copy-message'           => 'Сообщение о копировании',
                        'copy-message-duration'  => 'Длительность сообщения о копировании',
                        'default'                => 'По умолчанию',
                        'filterable'             => 'С фильтром',
                        'groupable'              => 'С группировкой',
                        'grow'                   => 'Растягивать',
                        'icon'                   => 'Значок',
                        'icon-color'             => 'Цвет значка',
                        'icon-position'          => 'Положение значка',
                        'label'                  => 'Метка',
                        'limit'                  => 'Лимит',
                        'line-clamp'             => 'Ограничение строк',
                        'money'                  => 'Денежный формат',
                        'placeholder'            => 'Заполнитель',
                        'prefix'                 => 'Префикс',
                        'searchable'             => 'С поиском',
                        'size'                   => 'Размер',
                        'sortable'               => 'С сортировкой',
                        'suffix'                 => 'Суффикс',
                        'toggleable'             => 'Можно скрывать',
                        'tooltip'                => 'Всплывающая подсказка',
                        'vertical-alignment'     => 'Вертикальное выравнивание',
                        'vertically-align-start' => 'Выровнять по верху',
                        'weight'                 => 'Насыщенность',
                        'width'                  => 'Ширина',
                        'words'                  => 'Слова',
                        'wrap-header'            => 'Переносить заголовок',
                        'column-span'            => 'Ширина в столбцах',
                        'helper-text'            => 'Пояснение',
                        'hint'                   => 'Подсказка',
                        'hint-color'             => 'Цвет подсказки',
                        'hint-icon'              => 'Значок подсказки',
                    ],

                    'datetime' => [
                        'date'              => 'Дата',
                        'date-time'         => 'Дата и время',
                        'date-time-tooltip' => 'Подсказка с датой и временем',
                        'since'             => 'Прошло времени',
                    ],
                ],
            ],

            'infolist-settings' => [
                'title' => 'Настройки карточки',

                'fields' => [
                    'setting'       => 'Параметр',
                    'value'         => 'Значение',
                    'color'         => 'Цвет',
                    'font-weight'   => 'Насыщенность шрифта',
                    'icon-position' => 'Положение значка',
                    'size'          => 'Размер',
                    'add-setting'   => 'Добавить параметр',

                    'color-options' => [
                        'danger'    => 'Опасность',
                        'info'      => 'Информация',
                        'primary'   => 'Основной',
                        'secondary' => 'Дополнительный',
                        'warning'   => 'Предупреждение',
                        'success'   => 'Успех',
                    ],

                    'font-weight-options' => [
                        'thin'        => 'Тонкий',
                        'extra-light' => 'Сверхсветлый',
                        'light'       => 'Светлый',
                        'normal'      => 'Обычный',
                        'medium'      => 'Средний',
                        'semi-bold'   => 'Полужирный',
                        'bold'        => 'Жирный',
                        'extra-bold'  => 'Сверхжирный',
                        'black'       => 'Чёрный',
                    ],

                    'icon-position-options' => [
                        'before'  => 'Перед',
                        'after'   => 'После',
                    ],

                    'size-options' => [
                        'extra-small' => 'Очень маленький',
                        'small'       => 'Маленький',
                        'medium'      => 'Средний',
                        'large'       => 'Большой',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'Выровнять по концу',
                        'alignment'              => 'Выравнивание',
                        'align-start'            => 'Выровнять по началу',
                        'badge'                  => 'Бейдж',
                        'boolean'                => 'Логическое значение',
                        'color'                  => 'Цвет',
                        'copyable'               => 'Можно копировать',
                        'copy-message'           => 'Сообщение о копировании',
                        'copy-message-duration'  => 'Длительность сообщения о копировании',
                        'default'                => 'По умолчанию',
                        'filterable'             => 'С фильтром',
                        'groupable'              => 'С группировкой',
                        'grow'                   => 'Растягивать',
                        'icon'                   => 'Значок',
                        'icon-color'             => 'Цвет значка',
                        'icon-position'          => 'Положение значка',
                        'label'                  => 'Метка',
                        'limit'                  => 'Лимит',
                        'line-clamp'             => 'Ограничение строк',
                        'money'                  => 'Денежный формат',
                        'placeholder'            => 'Заполнитель',
                        'prefix'                 => 'Префикс',
                        'searchable'             => 'С поиском',
                        'size'                   => 'Размер',
                        'sortable'               => 'С сортировкой',
                        'suffix'                 => 'Суффикс',
                        'toggleable'             => 'Можно скрывать',
                        'tooltip'                => 'Всплывающая подсказка',
                        'vertical-alignment'     => 'Вертикальное выравнивание',
                        'vertically-align-start' => 'Выровнять по верху',
                        'weight'                 => 'Насыщенность',
                        'width'                  => 'Ширина',
                        'words'                  => 'Слова',
                        'wrap-header'            => 'Переносить заголовок',
                        'column-span'            => 'Ширина в столбцах',
                        'helper-text'            => 'Пояснение',
                        'hint'                   => 'Подсказка',
                        'hint-color'             => 'Цвет подсказки',
                        'hint-icon'              => 'Значок подсказки',
                    ],

                    'datetime' => [
                        'date'              => 'Дата',
                        'date-time'         => 'Дата и время',
                        'date-time-tooltip' => 'Подсказка с датой и временем',
                        'since'             => 'Прошло времени',
                    ],

                    'checkbox-list' => [
                        'separator'                => 'Разделитель',
                        'list-with-line-breaks'    => 'Список с переносами строк',
                        'bulleted'                 => 'Маркированный',
                        'limit-list'               => 'Ограничить список',
                        'expandable-limited-list'  => 'Раскрываемый сокращённый список',
                    ],

                    'select' => [
                        'separator'                => 'Разделитель',
                        'list-with-line-breaks'    => 'Список с переносами строк',
                        'bulleted'                 => 'Маркированный',
                        'limit-list'               => 'Ограничить список',
                        'expandable-limited-list'  => 'Раскрываемый сокращённый список',
                    ],

                    'checkbox' => [
                        'boolean'     => 'Логическое значение',
                        'false-icon'  => 'Значок «нет»',
                        'true-icon'   => 'Значок «да»',
                        'true-color'  => 'Цвет «да»',
                        'false-color' => 'Цвет «нет»',
                    ],

                    'toggle' => [
                        'boolean'     => 'Логическое значение',
                        'false-icon'  => 'Значок «нет»',
                        'true-icon'   => 'Значок «да»',
                        'true-color'  => 'Цвет «да»',
                        'false-color' => 'Цвет «нет»',
                    ],
                ],
            ],

            'settings' => [
                'title' => 'Настройки',

                'fields' => [
                    'type'           => 'Тип',
                    'input-type'     => 'Тип ввода',
                    'is-multiselect' => 'Множественный выбор',
                    'sort-order'     => 'Порядок сортировки',

                    'type-options' => [
                        'text'          => 'Текстовое поле',
                        'textarea'      => 'Многострочный текст',
                        'select'        => 'Выпадающий список',
                        'checkbox'      => 'Флажок',
                        'radio'         => 'Переключатель',
                        'toggle'        => 'Выключатель',
                        'checkbox-list' => 'Список флажков',
                        'datetime'      => 'Выбор даты и времени',
                        'editor'        => 'Визуальный редактор',
                        'markdown'      => 'Редактор Markdown',
                        'color'         => 'Выбор цвета',
                    ],

                    'input-type-options' => [
                        'text'     => 'Текст',
                        'email'    => 'Эл. почта',
                        'numeric'  => 'Числовой',
                        'integer'  => 'Целое число',
                        'password' => 'Пароль',
                        'tel'      => 'Телефон',
                        'url'      => 'URL',
                        'color'    => 'Цвет',
                    ],
                ],
            ],

            'resource' => [
                'title' => 'Ресурс',

                'fields' => [
                    'plugin'   => 'Плагин',
                    'resource' => 'Ресурс',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'       => 'Код',
            'name'       => 'Название',
            'type'       => 'Тип',
            'resource'   => 'Ресурс',
            'created-at' => 'Создано',
        ],

        'groups' => [
        ],

        'filters' => [
            'type'     => 'Тип',
            'resource' => 'Ресурс',

            'type-options' => [
                'text'          => 'Текстовое поле',
                'textarea'      => 'Многострочный текст',
                'select'        => 'Выпадающий список',
                'checkbox'      => 'Флажок',
                'radio'         => 'Переключатель',
                'toggle'        => 'Выключатель',
                'checkbox-list' => 'Список флажков',
                'datetime'      => 'Выбор даты и времени',
                'editor'        => 'Визуальный редактор',
                'markdown'      => 'Редактор Markdown',
                'color'         => 'Выбор цвета',
            ],
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Поле восстановлено',
                    'body'  => 'Поле успешно восстановлено.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Поле удалено',
                    'body'  => 'Поле успешно удалено.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Поле удалено безвозвратно',
                    'body'  => 'Поле успешно удалено безвозвратно.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Поля восстановлены',
                    'body'  => 'Поля успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Поля удалены',
                    'body'  => 'Поля успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Поля удалены безвозвратно',
                    'body'  => 'Поля успешно удалены безвозвратно.',
                ],
            ],
        ],
    ],
];
