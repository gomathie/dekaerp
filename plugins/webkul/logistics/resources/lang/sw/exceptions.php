<?php

return [
    'not-enabled'       => 'Lojistiki haijawezeshwa kwa kampuni #:company. Msimamizi anaweza kuiwezesha chini ya Lojistiki > Mipangilio > Mipangilio.',
    'company-mismatch'  => ':record hii ni ya kampuni tofauti na :related yake.',
    'uninstall-blocked' => 'Lojistiki haiwezi kuondolewa wakati kuna usafirishaji :count: kuiondoa kunafuta data yote ya lojistiki. Hifadhi nakala ya hifadhidata, kisha weka LOGISTICS_ALLOW_UNINSTALL_WITH_DATA=true ili kuendelea.',

    'invalid-transition'      => 'Usafirishaji hauwezi kutoka “:from” kwenda “:to”.',
    'invalid-trip-transition' => 'Safari haiwezi kutoka “:from” kwenda “:to”.',

    'trip-no-vehicle'              => 'Panga gari kwa safari hii kabla ya kuituma.',
    'trip-no-driver'               => 'Panga dereva kwa safari hii kabla ya kuituma.',
    'trip-inactive-vehicle'        => 'Gari :vehicle limewekwa kwenye kumbukumbu na haliwezi kutumwa.',
    'trip-inactive-driver'         => 'Dereva :driver amewekwa kwenye kumbukumbu na hawezi kutumwa.',
    'trip-expired-license'         => 'Leseni ya dereva :driver iliisha muda tarehe :date.',
    'trip-shipment-not-confirmed'  => 'Usafirishaji :shipment lazima uthibitishwe kabla ya kuongezwa kwenye safari.',

    'nothing-to-invoice' => 'Usafirishaji :shipment hauna tozo zilizobaki za kutolewa ankara.',

    'receipt-required' => 'Gharama za kategoria “:category” zinahitaji risiti iambatishwe kabla ya kuwasilishwa au kuidhinishwa.',

    'expense-not-attributable' => 'Gharama lazima iunganishwe na usafirishaji, safari au gari kabla ya kuwasilishwa au kuidhinishwa.',

    'nothing-to-bill'           => 'Usafirishaji :shipment hauna gharama zilizoidhinishwa zilizobaki za kutozwa bili.',
    'missing-partner'           => 'Gharama “:expense” haina mlipwaji, hivyo hakuna wa kuandikiwa bili.',
    'missing-employee-contact'  => ':employee hana rekodi ya mhusika, hivyo urejeshaji wa gharama hauwezi kutozwa bili kwake. Ongeza mhusika kwa mfanyakazi kwanza.',
    'missing-bill-journal'      => 'Kampuni hii haina jarida la bili za wasambazaji. Chagua moja chini ya Lojistiki > Mipangilio > Mipangilio.',
    'missing-expense-account'   => 'Kampuni hii haina akaunti chaguo-msingi ya gharama. Chagua moja chini ya Lojistiki > Mipangilio > Mipangilio.',
];
