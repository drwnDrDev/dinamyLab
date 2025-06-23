<?php

use Spatie\ArrayToXml\ArrayToXml;

$factura = [
    '_attributes' => [
        'xmlns' => 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
        'xmlns:cac' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
        'xmlns:cbc' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
    ],
    'cbc:UBLVersionID' => '2.1',
    'cbc:CustomizationID' => '10', // Código de personalización DIAN
    'cbc:ProfileID' => 'DIAN-Factura',
    'cbc:ID' => 'FVE001',
    'cbc:IssueDate' => now()->toDateString(),

    'cac:AccountingSupplierParty' => [
        'cac:Party' => [
            'cac:PartyIdentification' => [
                'cbc:ID' => '900999888',
            ],
            'cac:PartyName' => [
                'cbc:Name' => 'Centro Emociona',
            ],
        ],
    ],

    'cac:AccountingCustomerParty' => [
        'cac:Party' => [
            'cac:PartyIdentification' => [
                'cbc:ID' => '1122334455',
            ],
            'cac:PartyName' => [
                'cbc:Name' => 'Carlos Ramírez',
            ],
        ],
    ],

    'cac:InvoiceLine' => [
        [
            'cbc:ID' => '1',
            'cbc:InvoicedQuantity' => ['_attributes' => ['unitCode' => 'NIU'], '_value' => '1'],
            'cbc:LineExtensionAmount' => ['_attributes' => ['currencyID' => 'COP'], '_value' => '50000'],
            'cac:Item' => [
                'cbc:Description' => 'Consulta psicológica presencial',
            ],
            'cac:Price' => [
                'cbc:PriceAmount' => ['_attributes' => ['currencyID' => 'COP'], '_value' => '50000'],
            ],
        ],
    ],

    'cac:LegalMonetaryTotal' => [
        'cbc:LineExtensionAmount' => ['_attributes' => ['currencyID' => 'COP'], '_value' => '50000'],
        'cbc:PayableAmount' => ['_attributes' => ['currencyID' => 'COP'], '_value' => '50000'],
    ],
];

$xml = ArrayToXml::convert(['Invoice' => $factura], 'Invoice', true, 'UTF-8');

file_put_contents(storage_path('app/factura_ejemplo.xml'), $xml);

echo "Factura UBL generada con éxito.";
