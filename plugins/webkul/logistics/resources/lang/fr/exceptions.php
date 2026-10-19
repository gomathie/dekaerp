<?php

return [
    'not-enabled'       => 'La logistique n’est pas activée pour la société n° :company. Un administrateur peut l’activer dans Logistique > Configuration > Paramètres.',
    'company-mismatch'  => 'Ce :record appartient à une société différente de son :related.',
    'uninstall-blocked' => 'La logistique ne peut pas être désinstallée tant qu’il existe :count expédition(s) : sa désinstallation supprime toutes les données logistiques. Sauvegardez la base de données, puis définissez LOGISTICS_ALLOW_UNINSTALL_WITH_DATA=true pour continuer.',

    'invalid-transition'      => 'Une expédition ne peut pas passer de « :from » à « :to ».',
    'invalid-trip-transition' => 'Un trajet ne peut pas passer de « :from » à « :to ».',

    'trip-no-vehicle'              => 'Affectez un véhicule à ce trajet avant de l’expédier.',
    'trip-no-driver'               => 'Affectez un chauffeur à ce trajet avant de l’expédier.',
    'trip-inactive-vehicle'        => 'Le véhicule :vehicle est archivé et ne peut pas être expédié.',
    'trip-inactive-driver'         => 'Le chauffeur :driver est archivé et ne peut pas être expédié.',
    'trip-expired-license'         => 'Le permis du chauffeur :driver a expiré le :date.',
    'trip-shipment-not-confirmed'  => 'L’expédition :shipment doit être confirmée avant de pouvoir être ajoutée à un trajet.',

    'nothing-to-invoice' => 'L’expédition :shipment n’a plus de frais facturables à facturer.',

    'receipt-required' => 'Les dépenses de la catégorie « :category » doivent comporter un justificatif avant de pouvoir être soumises ou approuvées.',

    'expense-not-attributable' => 'Une dépense doit être liée à une expédition, un trajet ou un véhicule avant de pouvoir être soumise ou approuvée.',

    'nothing-to-bill'           => 'L’expédition :shipment n’a plus de dépenses approuvées à facturer.',
    'missing-partner'           => 'La dépense « :expense » n’a pas de bénéficiaire ; il n’y a donc personne à qui établir la facture.',
    'missing-employee-contact'  => ':employee n’a pas de fiche de contact ; aucun remboursement ne peut donc lui être facturé. Ajoutez d’abord un contact à l’employé.',
    'missing-bill-journal'      => 'Cette société n’a pas de journal de factures fournisseurs défini. Choisissez-en un dans Logistique > Configuration > Paramètres.',
    'missing-expense-account'   => 'Cette société n’a pas de compte de dépenses par défaut défini. Choisissez-en un dans Logistique > Configuration > Paramètres.',
];
