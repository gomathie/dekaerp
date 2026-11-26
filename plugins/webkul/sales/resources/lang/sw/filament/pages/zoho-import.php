<?php

return [
    'navigation' => 'Ingiza kutoka Zoho Books',
    'title'      => 'Ingiza kutoka Zoho Books',
    'intro'      => 'Leta historia ya kampuni kutoka Zoho Books: chagua kampuni, pakia faili la kuhamisha, kagua jinsi akaunti, kodi na akaunti za fedha zitakavyolinganishwa, kisha ingiza. Hati zinarekodiwa kuanzia ya zamani zaidi zikiwa na namba zake za Zoho, kisha ukurasa unalinganisha Zoho na DEKA.',
    'empty'      => 'Bado hakuna uingizaji. Anza na “Pakia faili la Zoho”.',
    'unused'     => 'Akaunti :count za Zoho hazitumiwi na hati yoyote, kwa hiyo zimeachwa.',

    'propose' => [
        'label'       => 'Pakia faili la Zoho',
        'description' => 'Faili la kuhamisha (JSON, hadi MB 10) linakaguliwa na kulinganishwa na kampuni. Hakuna kinachorekodiwa bado.',
        'company'     => 'Kampuni',
        'file'        => 'Faili la Zoho Books (JSON)',
        'done'        => 'Ulinganisho uko tayari. Ukague hapa chini, kisha ingiza.',
    ],

    'propose-options' => [
        'numbering'      => 'Namba za hati mpya',
        'numbering-deka' => 'Mfululizo wa kila mwaka wa DEKA (hati zilizoingizwa zinabaki na namba zake za Zoho)',
        'numbering-zoho' => 'Endeleza kihesabu cha Zoho',
        'skip-samples'   => 'Ruka mawasiliano ya majaribio ya Zoho (“(Sample)”) yasiyotumiwa na hati yoyote',
    ],

    'compare' => [
        'label'       => 'Linganisha na faili jipya la Zoho',
        'description' => 'Kwa kipindi cha mifumo miwili: pakia faili jipya la Zoho ili kulinganisha na kilichopo DEKA sasa, pamoja na kilichoingizwa moja kwa moja DEKA. Hakuna kinachorekodiwa.',
        'queued'      => 'Ulinganisho umeanza. Ukurasa huu unaufuatilia.',
    ],

    'download' => [
        'label' => 'Pakua ulinganisho (CSV)',
    ],

    'unmatched' => [
        'none' => 'Kila ankara, malipo na gharama iko kwenye mifumo yote miwili.',
    ],

    'modes' => [
        'import'  => 'Uingizaji',
        'compare' => 'Ulinganisho',
    ],

    'import' => [
        'label'       => 'Ingiza',
        'confirm'     => 'Ingiza kwenye :company?',
        'description' => 'Historia yote inarekodiwa kwenye kampuni hii. Haiwezi kutenduliwa hapa; uingizaji uliosimama unaendelea ulipoishia ukianzishwa tena.',
        'queued'      => 'Uingizaji umeanza. Ukurasa huu unaufuatilia.',
    ],

    'run' => [
        'heading'    => 'Uingizaji kwenye :company',
        'status'     => 'Hali',
        'started-by' => 'Ulianzishwa na',
        'source'     => 'Kwenye faili',
        'working'    => 'Inaingiza. Inaweza kuchukua dakika kadhaa; ukurasa unajisasisha wenyewe.',
    ],

    'statuses' => [
        'proposed' => 'Inasubiri idhini yako',
        'queued'   => 'Kwenye foleni',
        'running'  => 'Inaingiza',
        'finished' => 'Imekamilika',
        'failed'   => 'Imesimama',
    ],

    'sections' => [
        'accounts'   => 'Akaunti',
        'taxes'      => 'Kodi',
        'banks'      => 'Akaunti za benki na fedha taslimu',
        'records'    => 'Bidhaa, mawasiliano na namba',
        'result'     => 'Kilichorekodiwa',
        'unmatched'  => 'Hati zilizo kwenye mfumo mmoja tu',
        'comparison' => 'Zoho ikilinganishwa na DEKA',
    ],

    'columns' => [
        'zoho'       => 'Zoho',
        'uses'       => 'Matumizi',
        'action'     => 'Hatua',
        'deka'       => 'DEKA',
        'section'    => 'Sehemu',
        'row'        => 'Safu',
        'difference' => 'Tofauti',
    ],

    'actions' => [
        'map'    => 'tumia iliyopo',
        'create' => 'unda',
        'unused' => 'haitumiki',
    ],

    'records' => [
        'items'          => 'Bidhaa: :create za kuunda, :map zimelinganishwa na zilizopo.',
        'contacts'       => 'Mawasiliano: :create ya kuunda, :map yamelinganishwa na yaliyopo.',
        'numbering-deka' => ':kind mpya zinatumia mfululizo wa kila mwaka wa DEKA; zilizoingizwa zinabaki na namba za Zoho.',
        'numbering'      => ':kind mpya zinaendelea na :next.',
    ],

    'comparison' => [
        'all-ok'      => 'Safu zote zinalingana.',
        'differences' => 'Safu :count zinatofautiana.',
    ],

    'errors' => [
        'empty-file' => 'Faili lililopakiwa halikuweza kusomwa.',
        'no-run'     => 'Pakia faili kwanza.',
    ],
];
