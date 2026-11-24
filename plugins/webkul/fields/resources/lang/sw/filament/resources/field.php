<?php

return [
    'navigation' => [
        'title' => 'Sehemu maalum',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'name'              => 'Jina',
                    'code'              => 'Msimbo',
                    'code-helper-text'  => 'Msimbo lazima uanze kwa herufi au kistari cha chini, na uwe na herufi, nambari na vistari vya chini pekee.',
                ],
            ],

            'options' => [
                'title' => 'Chaguo',

                'fields' => [
                    'add-option' => 'Ongeza chaguo',
                ],
            ],

            'form-settings' => [
                'title' => 'Mipangilio ya fomu',

                'field-sets' => [
                    'validations' => [
                        'title' => 'Kanuni za uthibitishaji',

                        'fields' => [
                            'validation'     => 'Uthibitishaji',
                            'field'          => 'Sehemu',
                            'value'          => 'Thamani',
                            'add-validation' => 'Ongeza uthibitishaji',
                        ],
                    ],

                    'additional-settings' => [
                        'title' => 'Mipangilio ya ziada',

                        'fields' => [
                            'setting'     => 'Mpangilio',
                            'value'       => 'Thamani',
                            'color'       => 'Rangi',
                            'add-setting' => 'Ongeza mpangilio',

                            'color-options' => [
                                'danger'    => 'Hatari',
                                'info'      => 'Taarifa',
                                'primary'   => 'Msingi',
                                'secondary' => 'Ya pili',
                                'warning'   => 'Onyo',
                                'success'   => 'Mafanikio',
                            ],

                            'grid-options' => [
                                'row'    => 'Safu mlalo',
                                'column' => 'Safu wima',
                            ],

                            'input-modes' => [
                                'text'     => 'Maandishi',
                                'email'    => 'Barua pepe',
                                'numeric'  => 'Nambari',
                                'integer'  => 'Nambari kamili',
                                'password' => 'Nenosiri',
                                'tel'      => 'Simu',
                                'url'      => 'URL',
                                'color'    => 'Rangi',
                                'none'     => 'Hakuna',
                                'decimal'  => 'Desimali',
                                'search'   => 'Tafuta',
                                'url'      => 'URL',
                            ],
                        ],
                    ],
                ],

                'validations' => [
                    'common' => [
                        'gt'                   => 'Kubwa kuliko',
                        'gte'                  => 'Kubwa kuliko au sawa na',
                        'lt'                   => 'Ndogo kuliko',
                        'lte'                  => 'Ndogo kuliko au sawa na',
                        'max-size'             => 'Ukubwa wa juu',
                        'min-size'             => 'Ukubwa wa chini',
                        'multiple-of'          => 'Kigawe cha',
                        'nullable'             => 'Inaweza kuwa tupu',
                        'prohibited'           => 'Imekatazwa',
                        'prohibited-if'        => 'Imekatazwa ikiwa',
                        'prohibited-unless'    => 'Imekatazwa isipokuwa',
                        'prohibits'            => 'Inakataza',
                        'required'             => 'Inahitajika',
                        'required-if'          => 'Inahitajika ikiwa',
                        'required-if-accepted' => 'Inahitajika ikikubaliwa',
                        'required-unless'      => 'Inahitajika isipokuwa',
                        'required-with'        => 'Inahitajika pamoja na',
                        'required-with-all'    => 'Inahitajika pamoja na zote',
                        'required-without'     => 'Inahitajika bila',
                        'required-without-all' => 'Inahitajika bila zote',
                        'rules'                => 'Kanuni maalum',
                        'unique'               => 'Ya kipekee',
                    ],

                    'text' => [
                        'alpha-dash'        => 'Herufi, nambari na vistari',
                        'alpha-num'         => 'Herufi na nambari',
                        'ascii'             => 'ASCII',
                        'doesnt-end-with'   => 'Haiishii na',
                        'doesnt-start-with' => 'Haianzi na',
                        'ends-with'         => 'Inaishia na',
                        'filled'            => 'Imejazwa',
                        'ip'                => 'IP',
                        'ipv4'              => 'IPv4',
                        'ipv6'              => 'IPv6',
                        'length'            => 'Urefu',
                        'mac-address'       => 'Anwani ya MAC',
                        'max-length'        => 'Urefu wa juu',
                        'min-length'        => 'Urefu wa chini',
                        'regex'             => 'Regex',
                        'starts-with'       => 'Inaanza na',
                        'ulid'              => 'ULID',
                        'uuid'              => 'UUID',
                    ],

                    'textarea' => [
                        'filled'     => 'Imejazwa',
                        'max-length' => 'Urefu wa juu',
                        'min-length' => 'Urefu wa chini',
                    ],

                    'select' => [
                        'different'  => 'Tofauti na',
                        'exists'     => 'Ipo',
                        'in'         => 'Imo katika',
                        'not-in'     => 'Haimo katika',
                        'same'       => 'Sawa na',
                    ],

                    'radio' => [],

                    'checkbox' => [
                        'accepted' => 'Imekubaliwa',
                        'declined' => 'Imekataliwa',
                    ],

                    'toggle' => [
                        'accepted' => 'Imekubaliwa',
                        'declined' => 'Imekataliwa',
                    ],

                    'checkbox-list' => [
                        'in'        => 'Imo katika',
                        'max-items' => 'Vipengee vya juu',
                        'min-items' => 'Vipengee vya chini',
                    ],

                    'datetime' => [
                        'after'           => 'Baada ya',
                        'after-or-equal'  => 'Baada ya au sawa na',
                        'before'          => 'Kabla ya',
                        'before-or-equal' => 'Kabla ya au sawa na',
                    ],

                    'editor' => [
                        'filled'     => 'Imejazwa',
                        'max-length' => 'Urefu wa juu',
                        'min-length' => 'Urefu wa chini',
                    ],

                    'markdown' => [
                        'filled'     => 'Imejazwa',
                        'max-length' => 'Urefu wa juu',
                        'min-length' => 'Urefu wa chini',
                    ],

                    'color' => [
                        'hex-color' => 'Rangi ya Hex',
                    ],
                ],

                'settings' => [
                    'text' => [
                        'autocapitalize'    => 'Herufi kubwa kiotomatiki',
                        'autocomplete'      => 'Jaza kiotomatiki',
                        'autofocus'         => 'Lenga kiotomatiki',
                        'default'           => 'Thamani chaguomsingi',
                        'disabled'          => 'Imezimwa',
                        'helper-text'       => 'Maandishi ya msaada',
                        'hint'              => 'Kidokezo',
                        'hint-color'        => 'Rangi ya kidokezo',
                        'hint-icon'         => 'Aikoni ya kidokezo',
                        'id'                => 'Kitambulisho',
                        'input-mode'        => 'Hali ya uingizaji',
                        'mask'              => 'Kiolezo cha uingizaji',
                        'placeholder'       => 'Maandishi ya mfano',
                        'prefix'            => 'Kiambishi awali',
                        'prefix-icon'       => 'Aikoni ya kiambishi awali',
                        'prefix-icon-color' => 'Rangi ya aikoni ya kiambishi awali',
                        'read-only'         => 'Kusoma tu',
                        'step'              => 'Hatua',
                        'suffix'            => 'Kiambishi tamati',
                        'suffix-icon'       => 'Aikoni ya kiambishi tamati',
                        'suffix-icon-color' => 'Rangi ya aikoni ya kiambishi tamati',
                    ],

                    'textarea' => [
                        'autofocus'    => 'Lenga kiotomatiki',
                        'autosize'     => 'Ukubwa kiotomatiki',
                        'cols'         => 'Safu wima',
                        'default'      => 'Thamani chaguomsingi',
                        'disabled'     => 'Imezimwa',
                        'helperText'   => 'Maandishi ya msaada',
                        'hint'         => 'Kidokezo',
                        'hintColor'    => 'Rangi ya kidokezo',
                        'hintIcon'     => 'Aikoni ya kidokezo',
                        'id'           => 'Kitambulisho',
                        'placeholder'  => 'Maandishi ya mfano',
                        'read-only'    => 'Kusoma tu',
                        'rows'         => 'Safu mlalo',
                    ],

                    'select' => [
                        'default'                   => 'Thamani chaguomsingi',
                        'disabled'                  => 'Imezimwa',
                        'helper-text'               => 'Maandishi ya msaada',
                        'hint'                      => 'Kidokezo',
                        'hint-color'                => 'Rangi ya kidokezo',
                        'hint-icon'                 => 'Aikoni ya kidokezo',
                        'id'                        => 'Kitambulisho',
                        'loading-message'           => 'Ujumbe wa kupakia',
                        'no-search-results-message' => 'Ujumbe wa kukosa matokeo',
                        'options-limit'             => 'Kikomo cha chaguo',
                        'preload'                   => 'Pakia mapema',
                        'searchable'                => 'Inatafutika',
                        'search-debounce'           => 'Ucheleweshaji wa utafutaji',
                        'searching-message'         => 'Ujumbe wa kutafuta',
                        'search-prompt'             => 'Kidokezo cha utafutaji',
                    ],

                    'radio' => [
                        'default'     => 'Thamani chaguomsingi',
                        'disabled'    => 'Imezimwa',
                        'helper-text' => 'Maandishi ya msaada',
                        'hint'        => 'Kidokezo',
                        'hint-color'  => 'Rangi ya kidokezo',
                        'hint-icon'   => 'Aikoni ya kidokezo',
                        'id'          => 'Kitambulisho',
                    ],

                    'checkbox' => [
                        'default'     => 'Thamani chaguomsingi',
                        'disabled'    => 'Imezimwa',
                        'helper-text' => 'Maandishi ya msaada',
                        'hint'        => 'Kidokezo',
                        'hint-color'  => 'Rangi ya kidokezo',
                        'hint-icon'   => 'Aikoni ya kidokezo',
                        'id'          => 'Kitambulisho',
                        'inline'      => 'Kwa mstari mmoja',
                    ],

                    'toggle' => [
                        'default'     => 'Thamani chaguomsingi',
                        'disabled'    => 'Imezimwa',
                        'helper-text' => 'Maandishi ya msaada',
                        'hint'        => 'Kidokezo',
                        'hint-color'  => 'Rangi ya kidokezo',
                        'hint-icon'   => 'Aikoni ya kidokezo',
                        'id'          => 'Kitambulisho',
                        'off-color'   => 'Rangi ikiwa imezimwa',
                        'off-icon'    => 'Aikoni ikiwa imezimwa',
                        'on-color'    => 'Rangi ikiwa imewashwa',
                        'on-icon'     => 'Aikoni ikiwa imewashwa',
                    ],

                    'checkbox-list' => [
                        'bulk-toggleable'           => 'Chagua zote kwa pamoja',
                        'columns'                   => 'Safu wima',
                        'default'                   => 'Thamani chaguomsingi',
                        'disabled'                  => 'Imezimwa',
                        'grid-direction'            => 'Mwelekeo wa gridi',
                        'helper-text'               => 'Maandishi ya msaada',
                        'hint'                      => 'Kidokezo',
                        'hint-color'                => 'Rangi ya kidokezo',
                        'hint-icon'                 => 'Aikoni ya kidokezo',
                        'id'                        => 'Kitambulisho',
                        'max-items'                 => 'Vipengee vya juu',
                        'min-items'                 => 'Vipengee vya chini',
                        'no-search-results-message' => 'Ujumbe wa kukosa matokeo',
                        'searchable'                => 'Inatafutika',
                    ],

                    'datetime' => [
                        'close-on-date-selection' => 'Funga tarehe ikichaguliwa',
                        'default'                 => 'Thamani chaguomsingi',
                        'disabled'                => 'Imezimwa',
                        'disabled-dates'          => 'Tarehe zilizozimwa',
                        'display-format'          => 'Muundo wa kuonyesha',
                        'first-day-of-week'       => 'Siku ya kwanza ya wiki',
                        'format'                  => 'Muundo',
                        'helper-text'             => 'Maandishi ya msaada',
                        'hint'                    => 'Kidokezo',
                        'hint-color'              => 'Rangi ya kidokezo',
                        'hint-icon'               => 'Aikoni ya kidokezo',
                        'hours-step'              => 'Hatua ya saa',
                        'id'                      => 'Kitambulisho',
                        'locale'                  => 'Lugha na eneo',
                        'minutes-step'            => 'Hatua ya dakika',
                        'seconds'                 => 'Sekunde',
                        'seconds-step'            => 'Hatua ya sekunde',
                        'timezone'                => 'Saa za eneo',
                        'week-starts-on-monday'   => 'Wiki inaanza Jumatatu',
                        'week-starts-on-sunday'   => 'Wiki inaanza Jumapili',
                    ],

                    'editor' => [
                        'default'      => 'Thamani chaguomsingi',
                        'disabled'     => 'Imezimwa',
                        'helper-text'  => 'Maandishi ya msaada',
                        'hint'         => 'Kidokezo',
                        'hint-color'   => 'Rangi ya kidokezo',
                        'hint-icon'    => 'Aikoni ya kidokezo',
                        'id'           => 'Kitambulisho',
                        'placeholder'  => 'Maandishi ya mfano',
                        'read-only'    => 'Kusoma tu',
                    ],

                    'markdown' => [
                        'default'      => 'Thamani chaguomsingi',
                        'disabled'     => 'Imezimwa',
                        'helper-text'  => 'Maandishi ya msaada',
                        'hint'         => 'Kidokezo',
                        'hint-color'   => 'Rangi ya kidokezo',
                        'hint-icon'    => 'Aikoni ya kidokezo',
                        'id'           => 'Kitambulisho',
                        'placeholder'  => 'Maandishi ya mfano',
                        'read-only'    => 'Kusoma tu',
                    ],

                    'color' => [
                        'default'     => 'Thamani chaguomsingi',
                        'disabled'    => 'Imezimwa',
                        'helper-text' => 'Maandishi ya msaada',
                        'hint'        => 'Kidokezo',
                        'hint-color'  => 'Rangi ya kidokezo',
                        'hint-icon'   => 'Aikoni ya kidokezo',
                        'hsl'         => 'HSL',
                        'id'          => 'Kitambulisho',
                        'rgb'         => 'RGB',
                        'rgba'        => 'RGBA',
                    ],

                    'file' => [
                        'accepted-file-types'                   => 'Aina za faili zinazokubaliwa',
                        'append-files'                          => 'Ongeza faili mwishoni',
                        'deletable'                             => 'Inafutika',
                        'directory'                             => 'Folda',
                        'downloadable'                          => 'Inapakulika',
                        'fetch-file-information'                => 'Leta taarifa za faili',
                        'file-attachments-directory'            => 'Folda ya viambatisho',
                        'file-attachments-visibility'           => 'Mwonekano wa viambatisho',
                        'image'                                 => 'Picha',
                        'image-crop-aspect-ratio'               => 'Uwiano wa kukata picha',
                        'image-editor'                          => 'Kihariri picha',
                        'image-editor-aspect-ratios'            => 'Uwiano wa kihariri picha',
                        'image-editor-empty-fill-color'         => 'Rangi ya kujaza nafasi tupu',
                        'image-editor-mode'                     => 'Hali ya kihariri picha',
                        'image-preview-height'                  => 'Kimo cha onyesho la picha',
                        'image-resize-mode'                     => 'Hali ya kubadili ukubwa',
                        'image-resize-target-height'            => 'Kimo lengwa cha picha',
                        'image-resize-target-width'             => 'Upana lengwa wa picha',
                        'loading-indicator-position'            => 'Nafasi ya kiashiria cha kupakia',
                        'move-files'                            => 'Hamisha faili',
                        'openable'                              => 'Inafunguka',
                        'orient-images-from-exif'               => 'Elekeza picha kwa EXIF',
                        'panel-aspect-ratio'                    => 'Uwiano wa paneli',
                        'panel-layout'                          => 'Mpangilio wa paneli',
                        'previewable'                           => 'Inaonyesheka awali',
                        'remove-uploaded-file-button-position'  => 'Nafasi ya kitufe cha kuondoa faili',
                        'reorderable'                           => 'Inapangika upya',
                        'store-files'                           => 'Hifadhi faili',
                        'upload-button-position'                => 'Nafasi ya kitufe cha kupakia',
                        'uploading-message'                     => 'Ujumbe wa kupakia faili',
                        'upload-progress-indicator-position'    => 'Nafasi ya kiashiria cha maendeleo',
                        'visibility'                            => 'Mwonekano',
                    ],
                ],
            ],

            'table-settings' => [
                'title' => 'Mipangilio ya jedwali',

                'fields' => [
                    'use-in-table'  => 'Tumia kwenye jedwali',
                    'setting'       => 'Mpangilio',
                    'value'         => 'Thamani',
                    'color'         => 'Rangi',
                    'alignment'     => 'Upangaji',
                    'font-weight'   => 'Unene wa herufi',
                    'icon-position' => 'Nafasi ya aikoni',
                    'size'          => 'Ukubwa',
                    'add-setting'   => 'Ongeza mpangilio',

                    'color-options' => [
                        'danger'    => 'Hatari',
                        'info'      => 'Taarifa',
                        'primary'   => 'Msingi',
                        'secondary' => 'Ya pili',
                        'warning'   => 'Onyo',
                        'success'   => 'Mafanikio',
                    ],

                    'alignment-options' => [
                        'start'   => 'Mwanzo',
                        'left'    => 'Kushoto',
                        'center'  => 'Katikati',
                        'end'     => 'Mwisho',
                        'right'   => 'Kulia',
                        'justify' => 'Pande zote',
                        'between' => 'Nafasi kati',
                    ],

                    'font-weight-options' => [
                        'thin'        => 'Nyembamba',
                        'extra-light' => 'Nyepesi sana',
                        'light'       => 'Nyepesi',
                        'normal'      => 'Kawaida',
                        'medium'      => 'Wastani',
                        'semi-bold'   => 'Nzito kiasi',
                        'bold'        => 'Nzito',
                        'extra-bold'  => 'Nzito sana',
                        'black'       => 'Nzito kabisa',
                    ],

                    'icon-position-options' => [
                        'before'  => 'Kabla',
                        'after'   => 'Baada',
                    ],

                    'size-options' => [
                        'extra-small' => 'Ndogo sana',
                        'small'       => 'Ndogo',
                        'medium'      => 'Wastani',
                        'large'       => 'Kubwa',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'Panga mwisho',
                        'alignment'              => 'Upangaji',
                        'align-start'            => 'Panga mwanzo',
                        'badge'                  => 'Beji',
                        'boolean'                => 'Ndiyo/Hapana',
                        'color'                  => 'Rangi',
                        'copyable'               => 'Inanakilika',
                        'copy-message'           => 'Ujumbe wa kunakili',
                        'copy-message-duration'  => 'Muda wa ujumbe wa kunakili',
                        'default'                => 'Chaguomsingi',
                        'filterable'             => 'Inachujika',
                        'groupable'              => 'Inapangika kwa vikundi',
                        'grow'                   => 'Panuka',
                        'icon'                   => 'Aikoni',
                        'icon-color'             => 'Rangi ya aikoni',
                        'icon-position'          => 'Nafasi ya aikoni',
                        'label'                  => 'Lebo',
                        'limit'                  => 'Kikomo',
                        'line-clamp'             => 'Kikomo cha mistari',
                        'money'                  => 'Fedha',
                        'placeholder'            => 'Maandishi ya mfano',
                        'prefix'                 => 'Kiambishi awali',
                        'searchable'             => 'Inatafutika',
                        'size'                   => 'Ukubwa',
                        'sortable'               => 'Inapangika',
                        'suffix'                 => 'Kiambishi tamati',
                        'toggleable'             => 'Inaweza kufichwa',
                        'tooltip'                => 'Kidokezo ibukizi',
                        'vertical-alignment'     => 'Upangaji wima',
                        'vertically-align-start' => 'Panga juu',
                        'weight'                 => 'Unene',
                        'width'                  => 'Upana',
                        'words'                  => 'Maneno',
                        'wrap-header'            => 'Funga kichwa',
                        'column-span'            => 'Upana wa safu',
                        'helper-text'            => 'Maandishi ya msaada',
                        'hint'                   => 'Kidokezo',
                        'hint-color'             => 'Rangi ya kidokezo',
                        'hint-icon'              => 'Aikoni ya kidokezo',
                    ],

                    'datetime' => [
                        'date'              => 'Tarehe',
                        'date-time'         => 'Tarehe na saa',
                        'date-time-tooltip' => 'Kidokezo cha tarehe na saa',
                        'since'             => 'Tangu',
                    ],
                ],
            ],

            'infolist-settings' => [
                'title' => 'Mipangilio ya ukurasa wa taarifa',

                'fields' => [
                    'setting'       => 'Mpangilio',
                    'value'         => 'Thamani',
                    'color'         => 'Rangi',
                    'font-weight'   => 'Unene wa herufi',
                    'icon-position' => 'Nafasi ya aikoni',
                    'size'          => 'Ukubwa',
                    'add-setting'   => 'Ongeza mpangilio',

                    'color-options' => [
                        'danger'    => 'Hatari',
                        'info'      => 'Taarifa',
                        'primary'   => 'Msingi',
                        'secondary' => 'Ya pili',
                        'warning'   => 'Onyo',
                        'success'   => 'Mafanikio',
                    ],

                    'font-weight-options' => [
                        'thin'        => 'Nyembamba',
                        'extra-light' => 'Nyepesi sana',
                        'light'       => 'Nyepesi',
                        'normal'      => 'Kawaida',
                        'medium'      => 'Wastani',
                        'semi-bold'   => 'Nzito kiasi',
                        'bold'        => 'Nzito',
                        'extra-bold'  => 'Nzito sana',
                        'black'       => 'Nzito kabisa',
                    ],

                    'icon-position-options' => [
                        'before'  => 'Kabla',
                        'after'   => 'Baada',
                    ],

                    'size-options' => [
                        'extra-small' => 'Ndogo sana',
                        'small'       => 'Ndogo',
                        'medium'      => 'Wastani',
                        'large'       => 'Kubwa',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'Panga mwisho',
                        'alignment'              => 'Upangaji',
                        'align-start'            => 'Panga mwanzo',
                        'badge'                  => 'Beji',
                        'boolean'                => 'Ndiyo/Hapana',
                        'color'                  => 'Rangi',
                        'copyable'               => 'Inanakilika',
                        'copy-message'           => 'Ujumbe wa kunakili',
                        'copy-message-duration'  => 'Muda wa ujumbe wa kunakili',
                        'default'                => 'Chaguomsingi',
                        'filterable'             => 'Inachujika',
                        'groupable'              => 'Inapangika kwa vikundi',
                        'grow'                   => 'Panuka',
                        'icon'                   => 'Aikoni',
                        'icon-color'             => 'Rangi ya aikoni',
                        'icon-position'          => 'Nafasi ya aikoni',
                        'label'                  => 'Lebo',
                        'limit'                  => 'Kikomo',
                        'line-clamp'             => 'Kikomo cha mistari',
                        'money'                  => 'Fedha',
                        'placeholder'            => 'Maandishi ya mfano',
                        'prefix'                 => 'Kiambishi awali',
                        'searchable'             => 'Inatafutika',
                        'size'                   => 'Ukubwa',
                        'sortable'               => 'Inapangika',
                        'suffix'                 => 'Kiambishi tamati',
                        'toggleable'             => 'Inaweza kufichwa',
                        'tooltip'                => 'Kidokezo ibukizi',
                        'vertical-alignment'     => 'Upangaji wima',
                        'vertically-align-start' => 'Panga juu',
                        'weight'                 => 'Unene',
                        'width'                  => 'Upana',
                        'words'                  => 'Maneno',
                        'wrap-header'            => 'Funga kichwa',
                        'column-span'            => 'Upana wa safu',
                        'helper-text'            => 'Maandishi ya msaada',
                        'hint'                   => 'Kidokezo',
                        'hint-color'             => 'Rangi ya kidokezo',
                        'hint-icon'              => 'Aikoni ya kidokezo',
                    ],

                    'datetime' => [
                        'date'              => 'Tarehe',
                        'date-time'         => 'Tarehe na saa',
                        'date-time-tooltip' => 'Kidokezo cha tarehe na saa',
                        'since'             => 'Tangu',
                    ],

                    'checkbox-list' => [
                        'separator'                => 'Kitenganishi',
                        'list-with-line-breaks'    => 'Orodha yenye mistari mipya',
                        'bulleted'                 => 'Yenye vitone',
                        'limit-list'               => 'Punguza orodha',
                        'expandable-limited-list'  => 'Orodha fupi inayopanuka',
                    ],

                    'select' => [
                        'separator'                => 'Kitenganishi',
                        'list-with-line-breaks'    => 'Orodha yenye mistari mipya',
                        'bulleted'                 => 'Yenye vitone',
                        'limit-list'               => 'Punguza orodha',
                        'expandable-limited-list'  => 'Orodha fupi inayopanuka',
                    ],

                    'checkbox' => [
                        'boolean'     => 'Ndiyo/Hapana',
                        'false-icon'  => 'Aikoni ya Hapana',
                        'true-icon'   => 'Aikoni ya Ndiyo',
                        'true-color'  => 'Rangi ya Ndiyo',
                        'false-color' => 'Rangi ya Hapana',
                    ],

                    'toggle' => [
                        'boolean'     => 'Ndiyo/Hapana',
                        'false-icon'  => 'Aikoni ya Hapana',
                        'true-icon'   => 'Aikoni ya Ndiyo',
                        'true-color'  => 'Rangi ya Ndiyo',
                        'false-color' => 'Rangi ya Hapana',
                    ],
                ],
            ],

            'settings' => [
                'title' => 'Mipangilio',

                'fields' => [
                    'type'           => 'Aina',
                    'input-type'     => 'Aina ya uingizaji',
                    'is-multiselect' => 'Chaguo nyingi',
                    'sort-order'     => 'Mpangilio wa mfuatano',

                    'type-options' => [
                        'text'          => 'Kisanduku cha maandishi',
                        'textarea'      => 'Eneo la maandishi',
                        'select'        => 'Orodha ya kuchagua',
                        'checkbox'      => 'Kisanduku cha kuteua',
                        'radio'         => 'Kitufe cha redio',
                        'toggle'        => 'Swichi',
                        'checkbox-list' => 'Orodha ya visanduku vya kuteua',
                        'datetime'      => 'Kichagua tarehe na saa',
                        'editor'        => 'Kihariri maandishi',
                        'markdown'      => 'Kihariri Markdown',
                        'color'         => 'Kichagua rangi',
                    ],

                    'input-type-options' => [
                        'text'     => 'Maandishi',
                        'email'    => 'Barua pepe',
                        'numeric'  => 'Nambari',
                        'integer'  => 'Nambari kamili',
                        'password' => 'Nenosiri',
                        'tel'      => 'Simu',
                        'url'      => 'URL',
                        'color'    => 'Rangi',
                    ],
                ],
            ],

            'resource' => [
                'title' => 'Rasilimali',

                'fields' => [
                    'plugin'   => 'Programu-jalizi',
                    'resource' => 'Rasilimali',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'       => 'Msimbo',
            'name'       => 'Jina',
            'type'       => 'Aina',
            'resource'   => 'Rasilimali',
            'created-at' => 'Iliundwa',
        ],

        'groups' => [
        ],

        'filters' => [
            'type'     => 'Aina',
            'resource' => 'Rasilimali',

            'type-options' => [
                'text'          => 'Kisanduku cha maandishi',
                'textarea'      => 'Eneo la maandishi',
                'select'        => 'Orodha ya kuchagua',
                'checkbox'      => 'Kisanduku cha kuteua',
                'radio'         => 'Kitufe cha redio',
                'toggle'        => 'Swichi',
                'checkbox-list' => 'Orodha ya visanduku vya kuteua',
                'datetime'      => 'Kichagua tarehe na saa',
                'editor'        => 'Kihariri maandishi',
                'markdown'      => 'Kihariri Markdown',
                'color'         => 'Kichagua rangi',
            ],
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Sehemu imerejeshwa',
                    'body'  => 'Sehemu imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sehemu imefutwa',
                    'body'  => 'Sehemu imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Sehemu imefutwa kabisa',
                    'body'  => 'Sehemu imefutwa kabisa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Sehemu zimerejeshwa',
                    'body'  => 'Sehemu zimerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sehemu zimefutwa',
                    'body'  => 'Sehemu zimefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Sehemu zimefutwa kabisa',
                    'body'  => 'Sehemu zimefutwa kabisa kwa mafanikio.',
                ],
            ],
        ],
    ],
];
