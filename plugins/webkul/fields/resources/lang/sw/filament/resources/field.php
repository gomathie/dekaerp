<?php

return [
    'navigation' => [
        'title' => 'Maalum Sehemu',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'name'              => 'Jina',
                    'code'              => 'msimbo',
                    'code-helper-text'  => 'Msimbo lazima anza na letter au underscore, na unaweza tu contain letters, numbers, na underscores.',
                ],
            ],

            'options' => [
                'title' => 'Options',

                'fields' => [
                    'add-option' => 'Ongeza Option',
                ],
            ],

            'form-settings' => [
                'title' => 'Fomu Mipangilio',

                'field-sets' => [
                    'validations' => [
                        'title' => 'Validations',

                        'fields' => [
                            'validation'     => 'Validation',
                            'field'          => 'Sehemu',
                            'value'          => 'Thamani',
                            'add-validation' => 'Ongeza Validation',
                        ],
                    ],

                    'additional-settings' => [
                        'title' => 'Additional Mipangilio',

                        'fields' => [
                            'setting'     => 'Mpangilio',
                            'value'       => 'Thamani',
                            'color'       => 'Rangi',
                            'add-setting' => 'Ongeza Mpangilio',

                            'color-options' => [
                                'danger'    => 'Danger',
                                'info'      => 'Info',
                                'primary'   => 'Primary',
                                'secondary' => 'Secondary',
                                'warning'   => 'Onyo',
                                'success'   => 'Imefanikiwa',
                            ],

                            'grid-options' => [
                                'row'    => 'Mstari',
                                'column' => 'Safu',
                            ],

                            'input-modes' => [
                                'text'     => 'Text',
                                'email'    => 'Barua pepe',
                                'numeric'  => 'Numeric',
                                'integer'  => 'Integer',
                                'password' => 'Nenosiri',
                                'tel'      => 'Telephone',
                                'url'      => 'URL',
                                'color'    => 'Rangi',
                                'none'     => 'Hakuna',
                                'decimal'  => 'Decimal',
                                'search'   => 'Tafuta',
                                'url'      => 'URL',
                            ],
                        ],
                    ],
                ],

                'validations' => [
                    'common' => [
                        'gt'                   => 'Greater Kuliko',
                        'gte'                  => 'Greater Kuliko au Equal',
                        'lt'                   => 'Chache Kuliko',
                        'lte'                  => 'Chache Kuliko au Equal',
                        'max-size'             => 'Max Ukubwa',
                        'min-size'             => 'Min Ukubwa',
                        'multiple-of'          => 'Multiple Ya',
                        'nullable'             => 'Nullable',
                        'prohibited'           => 'Prohibited',
                        'prohibited-if'        => 'Prohibited Ikiwa',
                        'prohibited-unless'    => 'Prohibited Unless',
                        'prohibits'            => 'Prohibits',
                        'required'             => 'Inahitajika',
                        'required-if'          => 'Inahitajika Ikiwa',
                        'required-if-accepted' => 'Inahitajika Ikiwa Accepted',
                        'required-unless'      => 'Inahitajika Unless',
                        'required-with'        => 'Inahitajika Na',
                        'required-with-all'    => 'Inahitajika Na Zote',
                        'required-without'     => 'Inahitajika Bila',
                        'required-without-all' => 'Inahitajika Bila Zote',
                        'rules'                => 'Maalum Rules',
                        'unique'               => 'Unique',
                    ],

                    'text' => [
                        'alpha-dash'        => 'Alpha Dash',
                        'alpha-num'         => 'Alpha Numeric',
                        'ascii'             => 'ASCII',
                        'doesnt-end-with'   => "Doesn't Mwisho Na",
                        'doesnt-start-with' => "Doesn't Anza Na",
                        'ends-with'         => 'Ends Na',
                        'filled'            => 'Filled',
                        'ip'                => 'IP',
                        'ipv4'              => 'IPv4',
                        'ipv6'              => 'IPv6',
                        'length'            => 'Length',
                        'mac-address'       => 'MAC Anwani',
                        'max-length'        => 'Max Length',
                        'min-length'        => 'Min Length',
                        'regex'             => 'Regex',
                        'starts-with'       => 'Starts Na',
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
                        'exists'     => 'Ipo',
                        'in'         => 'Katika',
                        'not-in'     => 'Si Katika',
                        'same'       => 'Ile ile',
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
                        'in'        => 'Katika',
                        'max-items' => 'Max Vipengee',
                        'min-items' => 'Min Vipengee',
                    ],

                    'datetime' => [
                        'after'           => 'Baada ya',
                        'after-or-equal'  => 'Baada ya au Equal',
                        'before'          => 'Kabla ya',
                        'before-or-equal' => 'Kabla ya au Equal',
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
                        'hex-color' => 'Hex Rangi',
                    ],
                ],

                'settings' => [
                    'text' => [
                        'autocapitalize'    => 'Autocapitalize',
                        'autocomplete'      => 'Autocomplete',
                        'autofocus'         => 'Autofocus',
                        'default'           => 'Chaguo-msingi Thamani',
                        'disabled'          => 'Imezimwa',
                        'helper-text'       => 'Helper Text',
                        'hint'              => 'Hint',
                        'hint-color'        => 'Hint Rangi',
                        'hint-icon'         => 'Hint Ikon',
                        'id'                => 'Kitambulisho',
                        'input-mode'        => 'Input Mode',
                        'mask'              => 'Mask',
                        'placeholder'       => 'Placeholder',
                        'prefix'            => 'Kiambishi',
                        'prefix-icon'       => 'Kiambishi Ikon',
                        'prefix-icon-color' => 'Kiambishi Ikon Rangi',
                        'read-only'         => 'Kusoma Tu',
                        'step'              => 'Step',
                        'suffix'            => 'Kiambishi tamati',
                        'suffix-icon'       => 'Kiambishi tamati Ikon',
                        'suffix-icon-color' => 'Kiambishi tamati Ikon Rangi',
                    ],

                    'textarea' => [
                        'autofocus'    => 'Autofocus',
                        'autosize'     => 'Autosize',
                        'cols'         => 'Safu',
                        'default'      => 'Chaguo-msingi Thamani',
                        'disabled'     => 'Imezimwa',
                        'helperText'   => 'Helper Text',
                        'hint'         => 'Hint',
                        'hintColor'    => 'Hint Rangi',
                        'hintIcon'     => 'Hint Ikon',
                        'id'           => 'Kitambulisho',
                        'placeholder'  => 'Placeholder',
                        'read-only'    => 'Kusoma Tu',
                        'rows'         => 'Mistari',
                    ],

                    'select' => [
                        'default'                   => 'Chaguo-msingi Thamani',
                        'disabled'                  => 'Imezimwa',
                        'helper-text'               => 'Helper Text',
                        'hint'                      => 'Hint',
                        'hint-color'                => 'Hint Rangi',
                        'hint-icon'                 => 'Hint Ikon',
                        'id'                        => 'Kitambulisho',
                        'loading-message'           => 'Inapakia Ujumbe',
                        'no-search-results-message' => 'Hakuna Tafuta Matokeo Ujumbe',
                        'options-limit'             => 'Options Limit',
                        'preload'                   => 'Preload',
                        'searchable'                => 'Searchable',
                        'search-debounce'           => 'Tafuta Debounce',
                        'searching-message'         => 'Searching Ujumbe',
                        'search-prompt'             => 'Tafuta Prompt',
                    ],

                    'radio' => [
                        'default'     => 'Chaguo-msingi Thamani',
                        'disabled'    => 'Imezimwa',
                        'helper-text' => 'Helper Text',
                        'hint'        => 'Hint',
                        'hint-color'  => 'Hint Rangi',
                        'hint-icon'   => 'Hint Ikon',
                        'id'          => 'Kitambulisho',
                    ],

                    'checkbox' => [
                        'default'     => 'Chaguo-msingi Thamani',
                        'disabled'    => 'Imezimwa',
                        'helper-text' => 'Helper Text',
                        'hint'        => 'Hint',
                        'hint-color'  => 'Hint Rangi',
                        'hint-icon'   => 'Hint Ikon',
                        'id'          => 'Kitambulisho',
                        'inline'      => 'Inline',
                    ],

                    'toggle' => [
                        'default'     => 'Chaguo-msingi Thamani',
                        'disabled'    => 'Imezimwa',
                        'helper-text' => 'Helper Text',
                        'hint'        => 'Hint',
                        'hint-color'  => 'Hint Rangi',
                        'hint-icon'   => 'Hint Ikon',
                        'id'          => 'Kitambulisho',
                        'off-color'   => 'Zima Rangi',
                        'off-icon'    => 'Zima Ikon',
                        'on-color'    => 'Washa Rangi',
                        'on-icon'     => 'Washa Ikon',
                    ],

                    'checkbox-list' => [
                        'bulk-toggleable'           => 'Bulk Toggleable',
                        'columns'                   => 'Safu',
                        'default'                   => 'Chaguo-msingi Thamani',
                        'disabled'                  => 'Imezimwa',
                        'grid-direction'            => 'Grid Direction',
                        'helper-text'               => 'Helper Text',
                        'hint'                      => 'Hint',
                        'hint-color'                => 'Hint Rangi',
                        'hint-icon'                 => 'Hint Ikon',
                        'id'                        => 'Kitambulisho',
                        'max-items'                 => 'Max Vipengee',
                        'min-items'                 => 'Min Vipengee',
                        'no-search-results-message' => 'Hakuna Tafuta Matokeo Ujumbe',
                        'searchable'                => 'Searchable',
                    ],

                    'datetime' => [
                        'close-on-date-selection' => 'Funga washa Tarehe Selection',
                        'default'                 => 'Chaguo-msingi Thamani',
                        'disabled'                => 'Imezimwa',
                        'disabled-dates'          => 'Imezimwa Dates',
                        'display-format'          => 'Display Umbizo',
                        'first-day-of-week'       => 'Kwanza Siku ya Wiki',
                        'format'                  => 'Umbizo',
                        'helper-text'             => 'Helper Text',
                        'hint'                    => 'Hint',
                        'hint-color'              => 'Hint Rangi',
                        'hint-icon'               => 'Hint Ikon',
                        'hours-step'              => 'Saa Step',
                        'id'                      => 'Kitambulisho',
                        'locale'                  => 'Locale',
                        'minutes-step'            => 'Dakika Step',
                        'seconds'                 => 'Seconds',
                        'seconds-step'            => 'Seconds Step',
                        'timezone'                => 'Timezone',
                        'week-starts-on-monday'   => 'Wiki Starts washa Jumatatu',
                        'week-starts-on-sunday'   => 'Wiki Starts washa Jumapili',
                    ],

                    'editor' => [
                        'default'      => 'Chaguo-msingi Thamani',
                        'disabled'     => 'Imezimwa',
                        'helper-text'  => 'Helper Text',
                        'hint'         => 'Hint',
                        'hint-color'   => 'Hint Rangi',
                        'hint-icon'    => 'Hint Ikon',
                        'id'           => 'Kitambulisho',
                        'placeholder'  => 'Placeholder',
                        'read-only'    => 'Kusoma Tu',
                    ],

                    'markdown' => [
                        'default'      => 'Chaguo-msingi Thamani',
                        'disabled'     => 'Imezimwa',
                        'helper-text'  => 'Helper Text',
                        'hint'         => 'Hint',
                        'hint-color'   => 'Hint Rangi',
                        'hint-icon'    => 'Hint Ikon',
                        'id'           => 'Kitambulisho',
                        'placeholder'  => 'Placeholder',
                        'read-only'    => 'Kusoma Tu',
                    ],

                    'color' => [
                        'default'     => 'Chaguo-msingi Thamani',
                        'disabled'    => 'Imezimwa',
                        'helper-text' => 'Helper Text',
                        'hint'        => 'Hint',
                        'hint-color'  => 'Hint Rangi',
                        'hint-icon'   => 'Hint Ikon',
                        'hsl'         => 'HSL',
                        'id'          => 'Kitambulisho',
                        'rgb'         => 'RGB',
                        'rgba'        => 'RGBA',
                    ],

                    'file' => [
                        'accepted-file-types'                   => 'Accepted Faili Types',
                        'append-files'                          => 'Append Faili',
                        'deletable'                             => 'Deletable',
                        'directory'                             => 'Directory',
                        'downloadable'                          => 'Downloadable',
                        'fetch-file-information'                => 'Fetch Faili Taarifa',
                        'file-attachments-directory'            => 'Faili Viambatisho Directory',
                        'file-attachments-visibility'           => 'Faili Viambatisho Visibility',
                        'image'                                 => 'Picha',
                        'image-crop-aspect-ratio'               => 'Picha Crop Aspect Ratio',
                        'image-editor'                          => 'Picha Editor',
                        'image-editor-aspect-ratios'            => 'Picha Editor Aspect Ratios',
                        'image-editor-empty-fill-color'         => 'Picha Editor Tupu Fill Rangi',
                        'image-editor-mode'                     => 'Picha Editor Mode',
                        'image-preview-height'                  => 'Picha Preview Urefu',
                        'image-resize-mode'                     => 'Picha Resize Mode',
                        'image-resize-target-height'            => 'Picha Resize Lengwa Urefu',
                        'image-resize-target-width'             => 'Picha Resize Lengwa Upana',
                        'loading-indicator-position'            => 'Inapakia Indicator Cheo',
                        'move-files'                            => 'Hamisha Faili',
                        'openable'                              => 'Openable',
                        'orient-images-from-exif'               => 'Orient Images kutoka EXIF',
                        'panel-aspect-ratio'                    => 'Panel Aspect Ratio',
                        'panel-layout'                          => 'Panel Layout',
                        'previewable'                           => 'Previewable',
                        'remove-uploaded-file-button-position'  => 'Ondoa Uploaded Faili Button Cheo',
                        'reorderable'                           => 'Reorderable',
                        'store-files'                           => 'Store Faili',
                        'upload-button-position'                => 'Pakia Button Cheo',
                        'uploading-message'                     => 'Uploading Ujumbe',
                        'upload-progress-indicator-position'    => 'Pakia Maendeleo Indicator Cheo',
                        'visibility'                            => 'Visibility',
                    ],
                ],
            ],

            'table-settings' => [
                'title' => 'Jedwali Mipangilio',

                'fields' => [
                    'use-in-table'  => 'Tumia katika Jedwali',
                    'setting'       => 'Mpangilio',
                    'value'         => 'Thamani',
                    'color'         => 'Rangi',
                    'alignment'     => 'Alignment',
                    'font-weight'   => 'Font Uzito',
                    'icon-position' => 'Ikon Cheo',
                    'size'          => 'Ukubwa',
                    'add-setting'   => 'Ongeza Mpangilio',

                    'color-options' => [
                        'danger'    => 'Danger',
                        'info'      => 'Info',
                        'primary'   => 'Primary',
                        'secondary' => 'Secondary',
                        'warning'   => 'Onyo',
                        'success'   => 'Imefanikiwa',
                    ],

                    'alignment-options' => [
                        'start'   => 'Anza',
                        'left'    => 'Iliyobaki',
                        'center'  => 'Center',
                        'end'     => 'Mwisho',
                        'right'   => 'Kulia',
                        'justify' => 'Justify',
                        'between' => 'Kati ya',
                    ],

                    'font-weight-options' => [
                        'thin'        => 'Thin',
                        'extra-light' => 'Extra Nyepesi',
                        'light'       => 'Nyepesi',
                        'normal'      => 'Kawaida',
                        'medium'      => 'Wastani',
                        'semi-bold'   => 'Semi Bold',
                        'bold'        => 'Bold',
                        'extra-bold'  => 'Extra Bold',
                        'black'       => 'Black',
                    ],

                    'icon-position-options' => [
                        'before'  => 'Kabla ya',
                        'after'   => 'Baada ya',
                    ],

                    'size-options' => [
                        'extra-small' => 'Extra Ndogo',
                        'small'       => 'Ndogo',
                        'medium'      => 'Wastani',
                        'large'       => 'Kubwa',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'Align Mwisho',
                        'alignment'              => 'Alignment',
                        'align-start'            => 'Align Anza',
                        'badge'                  => 'Badge',
                        'boolean'                => 'Boolean',
                        'color'                  => 'Rangi',
                        'copyable'               => 'Copyable',
                        'copy-message'           => 'Nakili Ujumbe',
                        'copy-message-duration'  => 'Nakili Ujumbe Duration',
                        'default'                => 'Chaguo-msingi',
                        'filterable'             => 'Filterable',
                        'groupable'              => 'Groupable',
                        'grow'                   => 'Grow',
                        'icon'                   => 'Ikon',
                        'icon-color'             => 'Ikon Rangi',
                        'icon-position'          => 'Ikon Cheo',
                        'label'                  => 'Lebo',
                        'limit'                  => 'Limit',
                        'line-clamp'             => 'Mstari Clamp',
                        'money'                  => 'Money',
                        'placeholder'            => 'Placeholder',
                        'prefix'                 => 'Kiambishi',
                        'searchable'             => 'Searchable',
                        'size'                   => 'Ukubwa',
                        'sortable'               => 'Sortable',
                        'suffix'                 => 'Kiambishi tamati',
                        'toggleable'             => 'Toggleable',
                        'tooltip'                => 'Tooltip',
                        'vertical-alignment'     => 'Vertical Alignment',
                        'vertically-align-start' => 'Vertically Align Anza',
                        'weight'                 => 'Uzito',
                        'width'                  => 'Upana',
                        'words'                  => 'Words',
                        'wrap-header'            => 'Wrap Header',
                        'column-span'            => 'Safu Span',
                        'helper-text'            => 'Helper Text',
                        'hint'                   => 'Hint',
                        'hint-color'             => 'Hint Rangi',
                        'hint-icon'              => 'Hint Ikon',
                    ],

                    'datetime' => [
                        'date'              => 'Tarehe',
                        'date-time'         => 'Tarehe Time',
                        'date-time-tooltip' => 'Tarehe Time Tooltip',
                        'since'             => 'Since',
                    ],
                ],
            ],

            'infolist-settings' => [
                'title' => 'Infolist Mipangilio',

                'fields' => [
                    'setting'       => 'Mpangilio',
                    'value'         => 'Thamani',
                    'color'         => 'Rangi',
                    'font-weight'   => 'Font Uzito',
                    'icon-position' => 'Ikon Cheo',
                    'size'          => 'Ukubwa',
                    'add-setting'   => 'Ongeza Mpangilio',

                    'color-options' => [
                        'danger'    => 'Danger',
                        'info'      => 'Info',
                        'primary'   => 'Primary',
                        'secondary' => 'Secondary',
                        'warning'   => 'Onyo',
                        'success'   => 'Imefanikiwa',
                    ],

                    'font-weight-options' => [
                        'thin'        => 'Thin',
                        'extra-light' => 'Extra Nyepesi',
                        'light'       => 'Nyepesi',
                        'normal'      => 'Kawaida',
                        'medium'      => 'Wastani',
                        'semi-bold'   => 'Semi Bold',
                        'bold'        => 'Bold',
                        'extra-bold'  => 'Extra Bold',
                        'black'       => 'Black',
                    ],

                    'icon-position-options' => [
                        'before'  => 'Kabla ya',
                        'after'   => 'Baada ya',
                    ],

                    'size-options' => [
                        'extra-small' => 'Extra Ndogo',
                        'small'       => 'Ndogo',
                        'medium'      => 'Wastani',
                        'large'       => 'Kubwa',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'Align Mwisho',
                        'alignment'              => 'Alignment',
                        'align-start'            => 'Align Anza',
                        'badge'                  => 'Badge',
                        'boolean'                => 'Boolean',
                        'color'                  => 'Rangi',
                        'copyable'               => 'Copyable',
                        'copy-message'           => 'Nakili Ujumbe',
                        'copy-message-duration'  => 'Nakili Ujumbe Duration',
                        'default'                => 'Chaguo-msingi',
                        'filterable'             => 'Filterable',
                        'groupable'              => 'Groupable',
                        'grow'                   => 'Grow',
                        'icon'                   => 'Ikon',
                        'icon-color'             => 'Ikon Rangi',
                        'icon-position'          => 'Ikon Cheo',
                        'label'                  => 'Lebo',
                        'limit'                  => 'Limit',
                        'line-clamp'             => 'Mstari Clamp',
                        'money'                  => 'Money',
                        'placeholder'            => 'Placeholder',
                        'prefix'                 => 'Kiambishi',
                        'searchable'             => 'Searchable',
                        'size'                   => 'Ukubwa',
                        'sortable'               => 'Sortable',
                        'suffix'                 => 'Kiambishi tamati',
                        'toggleable'             => 'Toggleable',
                        'tooltip'                => 'Tooltip',
                        'vertical-alignment'     => 'Vertical Alignment',
                        'vertically-align-start' => 'Vertically Align Anza',
                        'weight'                 => 'Uzito',
                        'width'                  => 'Upana',
                        'words'                  => 'Words',
                        'wrap-header'            => 'Wrap Header',
                        'column-span'            => 'Safu Span',
                        'helper-text'            => 'Helper Text',
                        'hint'                   => 'Hint',
                        'hint-color'             => 'Hint Rangi',
                        'hint-icon'              => 'Hint Ikon',
                    ],

                    'datetime' => [
                        'date'              => 'Tarehe',
                        'date-time'         => 'Tarehe Time',
                        'date-time-tooltip' => 'Tarehe Time Tooltip',
                        'since'             => 'Since',
                    ],

                    'checkbox-list' => [
                        'separator'                => 'Separator',
                        'list-with-line-breaks'    => 'Orodha na Mstari Breaks',
                        'bulleted'                 => 'Bulleted',
                        'limit-list'               => 'Limit Orodha',
                        'expandable-limited-list'  => 'Expandable Limited Orodha',
                    ],

                    'select' => [
                        'separator'                => 'Separator',
                        'list-with-line-breaks'    => 'Orodha na Mstari Breaks',
                        'bulleted'                 => 'Bulleted',
                        'limit-list'               => 'Limit Orodha',
                        'expandable-limited-list'  => 'Expandable Limited Orodha',
                    ],

                    'checkbox' => [
                        'boolean'     => 'Boolean',
                        'false-icon'  => 'Hapana Ikon',
                        'true-icon'   => 'Ndiyo Ikon',
                        'true-color'  => 'Ndiyo Rangi',
                        'false-color' => 'Hapana Rangi',
                    ],

                    'toggle' => [
                        'boolean'     => 'Boolean',
                        'false-icon'  => 'Hapana Ikon',
                        'true-icon'   => 'Ndiyo Ikon',
                        'true-color'  => 'Ndiyo Rangi',
                        'false-color' => 'Hapana Rangi',
                    ],
                ],
            ],

            'settings' => [
                'title' => 'Mipangilio',

                'fields' => [
                    'type'           => 'Aina',
                    'input-type'     => 'Input Aina',
                    'is-multiselect' => 'Ni Multiselect',
                    'sort-order'     => 'Sort Agizo',

                    'type-options' => [
                        'text'          => 'Text Input',
                        'textarea'      => 'Textarea',
                        'select'        => 'Chagua',
                        'checkbox'      => 'Checkbox',
                        'radio'         => 'Radio',
                        'toggle'        => 'Toggle',
                        'checkbox-list' => 'Checkbox Orodha',
                        'datetime'      => 'Tarehe Time Picker',
                        'editor'        => 'Rich Text Editor',
                        'markdown'      => 'Markdown Editor',
                        'color'         => 'Rangi Picker',
                    ],

                    'input-type-options' => [
                        'text'     => 'Text',
                        'email'    => 'Barua pepe',
                        'numeric'  => 'Numeric',
                        'integer'  => 'Integer',
                        'password' => 'Nenosiri',
                        'tel'      => 'Telephone',
                        'url'      => 'URL',
                        'color'    => 'Rangi',
                    ],
                ],
            ],

            'resource' => [
                'title' => 'Resource',

                'fields' => [
                    'plugin'   => 'Programu-jalizi',
                    'resource' => 'Resource',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'       => 'Msimbo',
            'name'       => 'Jina',
            'type'       => 'Aina',
            'resource'   => 'Resource',
            'created-at' => 'Imeundwa',
        ],

        'groups' => [
        ],

        'filters' => [
            'type'     => 'Aina',
            'resource' => 'Resource',

            'type-options' => [
                'text'          => 'Text Input',
                'textarea'      => 'Textarea',
                'select'        => 'Chagua',
                'checkbox'      => 'Checkbox',
                'radio'         => 'Radio',
                'toggle'        => 'Toggle',
                'checkbox-list' => 'Checkbox Orodha',
                'datetime'      => 'Tarehe Time Picker',
                'editor'        => 'Rich Text Editor',
                'markdown'      => 'Markdown Editor',
                'color'         => 'Rangi Picker',
            ],
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Sehemu restored',
                    'body'  => 'sehemu ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sehemu imefutwa',
                    'body'  => 'sehemu ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Sehemu force imefutwa',
                    'body'  => 'sehemu ina force imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Sehemu restored',
                    'body'  => 'sehemu ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sehemu imefutwa',
                    'body'  => 'sehemu ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Sehemu force imefutwa',
                    'body'  => 'sehemu ina force imefutwa successfully.',
                ],
            ],
        ],
    ],
];
