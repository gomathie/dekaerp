<?php

return [
    'not-enabled'       => 'Usafirishaji ni si imewashwa kwa kampuni #:company. msimamizi unaweza washa hii chini ya Usafirishaji > Usanidi > Mipangilio.',
    'company-mismatch'  => 'Hii :record belongs hadi different kampuni kuliko yake :related.',
    'uninstall-blocked' => 'Usafirishaji unaweza’t kuwa uninstalled wakati :count shehena(s) exist: uninstalling deletes zote usafirishaji data. Rudi juu database, kisha weka Usafirishaji_Ruhusu_UNINSTALL_Na_DATA=ndiyo hadi proceed.',

    'invalid-transition'      => 'shehena haiwezekani go kutoka “:from” hadi “:to”.',
    'invalid-trip-transition' => 'safari haiwezekani go kutoka “:from” hadi “:to”.',

    'trip-no-vehicle'              => 'Kabidhi gari hadi hii safari kabla ya dispatching hii.',
    'trip-no-driver'               => 'Kabidhi dereva hadi hii safari kabla ya dispatching hii.',
    'trip-inactive-vehicle'        => 'Gari :vehicle ni imehifadhiwa na haiwezekani kuwa dispatched.',
    'trip-inactive-driver'         => 'Dereva :driver ni imehifadhiwa na haiwezekani kuwa dispatched.',
    'trip-expired-license'         => 'Dereva :driver’s licence expired washa :date.',
    'trip-shipment-not-confirmed'  => 'Shehena :shipment lazima kuwa imethibitishwa kabla ya hii unaweza kuwa added hadi safari.',

    'nothing-to-invoice' => 'Shehena :shipment ina hakuna billable charges iliyobaki hadi ankara.',

    'receipt-required' => 'Gharama katika “:category” kategoria need stakabadhi attached kabla ya wao unaweza kuwa submitted au imeidhinishwa.',

    'expense-not-attributable' => 'gharama lazima kuwa linked hadi shehena, safari au gari kabla ya hii unaweza kuwa submitted au imeidhinishwa.',

    'nothing-to-bill'           => 'Shehena :shipment ina hakuna imeidhinishwa gharama iliyobaki hadi bili.',
    'missing-partner'           => 'Gharama “:expense” ina hakuna payee, hivyo ni nobody hadi fanya bili nje hadi.',
    'missing-employee-contact'  => ':employee ina hakuna mwasiliani rekodi, hivyo reimbursement haiwezekani kuwa billed hadi wao. Ongeza mwasiliani washa mfanyakazi kwanza.',
    'missing-bill-journal'      => 'Hii kampuni ina hakuna bili ya msambazaji jarida weka. Chagua one chini ya Usafirishaji > Usanidi > Mipangilio.',
    'missing-expense-account'   => 'Hii kampuni ina hakuna chaguo-msingi gharama akaunti weka. Chagua one chini ya Usafirishaji > Usanidi > Mipangilio.',
];
