<?php

return [
    'tabs' => [
        'help'       => 'Msaada',
        'developers' => 'Wasanidi',
    ],

    'heading' => 'Marejeo ya API',
    'intro'   => 'Unganisha programu zako na data ya kampuni yako. Kila ombi hufanywa kama mtumiaji, kwa tokeni, na huona tu kile mtumiaji huyo anaruhusiwa kuona.',
    'general' => 'Jumla',
    'empty'   => 'Kampuni hii bado haina moduli zenye API. Kuingia na kutoka hufanya kazi kwa kila kampuni.',
    'copy'    => 'Nakili',
    'copied'  => 'Imenakiliwa',

    'badges' => [
        'version'   => 'API ya :name v1',
        'total'     => '{0} Hakuna njia|{1} Njia 1|[2,*] Njia :count',
        'in_module' => ':count katika :module',
    ],

    'modules' => [
        'label'       => 'Moduli',
        'sales'       => 'Mauzo',
        'purchases'   => 'Manunuzi',
        'accounts'    => 'Uhasibu',
        'inventories' => 'Hesabu ya bidhaa',
        'products'    => 'Bidhaa',
        'partners'    => 'Mawasiliano',
        'projects'    => 'Miradi',
        'support'     => 'Mipangilio',
    ],

    'search' => [
        'placeholder' => 'Tafuta katika :module',
        'empty'       => 'Hakuna njia inayolingana.',
    ],

    'getting_started' => [
        'title'               => 'Kuanza',
        'base_url'            => 'URL ya msingi',
        'authentication'      => 'Uthibitishaji',
        'authentication_help' => 'Tuma tokeni yako kwenye kichwa cha Authorization cha kila ombi.',
        'token'               => 'Pata tokeni',
        'token_page'          => 'Toa moja katika Mipangilio › Tokeni za API, kwa mtumiaji aliyeundwa kwa ajili ya muunganisho.',
        'token_page_button'   => 'Fungua Tokeni za API',
        'token_login'         => 'Au muunganisho uingie kwa POST /admin/api/v1/login (hapa chini).',
        'scopes'              => 'Mawanda',
        'scopes_help'         => 'Tokeni ya read inaweza kufanya maombi ya GET tu. Kuunda, kubadilisha au kufuta chochote kunahitaji write.',
        'company'             => 'Kampuni ipi',
        'company_help'        => 'Tuma X-Company-Id yenye moja ya kampuni za mtumiaji wa tokeni. Bila hiyo, maombi huenda kwa kampuni chaguo-msingi ya mtumiaji.',
        'company_current'     => 'Kampuni unayofanyia kazi ina id :id.',
        'limits'              => 'Mipaka',
        'rate_limit'          => '{1} Ombi 1 kwa dakika kwa kila tokeni.|[2,*] Maombi :count kwa dakika kwa kila tokeni.',
        'expiry'              => 'Tokeni huisha baada ya :time.',
        'no_expiry'           => 'Tokeni haziishi.',
        'format'              => 'Muundo',
        'format_help'         => 'Tuma kichwa hiki kila mara, ili makosa yarudi kama JSON badala ya ukurasa wa wavuti.',
    ],

    'full_reference' => [
        'title'       => 'Marejeo kamili',
        'description' => 'Moduli na njia zote, pamoja na sehemu ya kujaribu maombi. Hufunguka kwenye kichupo kipya na inahitaji uwe umeingia.',
        'button'      => 'Fungua marejeo kamili',
    ],

    'headers' => [
        'authorization' => 'Bearer ikifuatiwa na tokeni yako.',
        'company'       => 'id ya kampuni ya kufanyia kazi.',
        'accept'        => 'application/json',
    ],

    'in' => [
        'path'   => 'njia',
        'query'  => 'hoja',
        'body'   => 'mwili',
        'header' => 'kichwa',
    ],

    'endpoint' => [
        'parameters'    => 'Vigezo',
        'name'          => 'Jina',
        'type'          => 'Aina',
        'description'   => 'Maelezo',
        'required'      => 'Lazima',
        'optional'      => 'Si lazima',
        'samples'       => 'Mifano ya maombi',
        'response'      => 'Jibu (:status)',
        'other_answers' => 'Majibu mengine:',
        'no_token'      => 'Hakuna tokeni inayohitajika',
    ],

    'answers' => [
        '401' => '401 hakuna tokeni, au imeisha muda',
        '403' => '403 mtumiaji au kampuni ya tokeni hairuhusiwi kufanya hivi',
        '422' => '422 data iliyotumwa si sahihi, na errors inataja kila sehemu',
        '429' => '429 maombi mengi mno, subiri dakika moja',
    ],
];
