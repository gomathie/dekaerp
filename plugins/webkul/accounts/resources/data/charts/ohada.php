<?php

/**
 * SYSCOHADA (revised 2017) chart for the 17 OHADA member states, Congo DRC
 * and Congo-Brazzaville included (accounting plan A4, 2026-11-14).
 *
 * Applied once, on top of the standard chart a company copies when its
 * accounting is set up (ChartLocalizer). Keys of "accounts" are codes of the
 * standard chart; each copied account keeps its id, so journals, settings
 * and taxes that point at it keep working, and only its code and name change
 * to the SYSCOHADA class (1 capital, 2 fixed assets, 3 stock, 4 third
 * parties, 5 treasury, 6 expenses, 7 income). Codes are padded to six digits.
 *
 * Taxes: the standard VAT rate per country. Not reviewed by a local
 * accountant yet: check the rates and levies (for example Congo's
 * "centime additionnel") before calling a company's setup compliant.
 */
return [
    'name'      => 'SYSCOHADA',
    'countries' => ['BJ', 'BF', 'CM', 'CF', 'KM', 'CG', 'CD', 'CI', 'GA', 'GN', 'GW', 'GQ', 'ML', 'NE', 'SN', 'TD', 'TG'],

    'accounts' => [
        '101000' => ['471100', 'Débiteurs divers'],
        '110100' => ['311000', 'Marchandises'],
        '110200' => ['381100', 'Marchandises en cours de route (réceptions)'],
        '110300' => ['381200', 'Marchandises en cours de route (livraisons)'],
        '110400' => ['341000', 'Produits en cours'],
        '110500' => ['351000', 'Services en cours'],
        '121000' => ['411000', 'Clients'],
        '121100' => ['409800', 'Fournisseurs, marchandises à recevoir'],
        '128000' => ['476000', 'Charges constatées d\'avance'],
        '131000' => ['445000', 'État, TVA récupérable'],
        '132000' => ['449000', 'État, créances diverses'],
        '141000' => ['409100', 'Fournisseurs, avances et acomptes versés'],
        '151000' => ['244000', 'Matériel et mobilier'],
        '191000' => ['270000', 'Immobilisations financières'],
        '201000' => ['471200', 'Créditeurs divers'],
        '211000' => ['401000', 'Fournisseurs'],
        '211100' => ['408000', 'Fournisseurs, factures non parvenues'],
        '212000' => ['477000', 'Produits constatés d\'avance'],
        '230000' => ['422000', 'Personnel, rémunérations dues'],
        '230100' => ['447200', 'État, impôts retenus sur salaires'],
        '230200' => ['431000', 'Sécurité sociale'],
        '251000' => ['443100', 'État, TVA facturée'],
        '252000' => ['444100', 'État, TVA due'],
        '291000' => ['162000', 'Emprunts et dettes auprès des établissements de crédit'],
        '301000' => ['101000', 'Capital social'],
        '302000' => ['465000', 'Associés, dividendes à payer'],
        '400000' => ['701000', 'Ventes de marchandises'],
        '441000' => ['776000', 'Gains de change'],
        '442000' => ['758100', 'Produits divers (écarts de caisse)'],
        '443000' => ['673000', 'Escomptes accordés'],
        '450000' => ['707000', 'Produits accessoires'],
        '500000' => ['601000', 'Achats de marchandises'],
        '600000' => ['638000', 'Autres charges externes'],
        '611000' => ['605600', 'Achats de petit matériel et outillage'],
        '612000' => ['622000', 'Locations et charges locatives'],
        '620000' => ['631000', 'Frais bancaires'],
        '630000' => ['661000', 'Rémunérations directes versées au personnel'],
        '641000' => ['676000', 'Pertes de change'],
        '642000' => ['658100', 'Charges diverses (écarts de caisse)'],
        '643000' => ['773000', 'Escomptes obtenus'],
        '961000' => ['638100', 'Frais de recherche et de développement'],
        '962000' => ['627000', 'Publicité, publications, relations publiques'],
        '101300' => ['411100', 'Clients, point de vente'],
        '201100' => ['561000', 'Banques, crédits de trésorerie'],
        '101401' => ['521000', 'Banques locales'],
        '101501' => ['571000', 'Caisse'],
        '101402' => ['471000', 'Compte d\'attente, banque'],
        '101701' => ['585000', 'Virements de fonds'],
        '101403' => ['513000', 'Chèques à encaisser'],
        '101404' => ['521800', 'Banques, paiements en cours'],
        '999999' => ['121000', 'Report à nouveau'],
    ],

    'extra_accounts' => [
        ['421000', 'Personnel, avances et acomptes', 'asset_current', true],
        ['442000', 'État, autres impôts et taxes', 'liability_current', false],
        ['641000', 'Impôts et taxes directs', 'expense', false],
        ['681000', 'Dotations aux amortissements d\'exploitation', 'expense_depreciation', false],
        ['706000', 'Services vendus', 'income', false],
    ],

    'taxes' => [
        'CD' => ['TVA 16 %', 16],
        'CG' => ['TVA 18 %', 18],
    ],

    'exempt_tax' => 'Exonéré 0 %',
];
