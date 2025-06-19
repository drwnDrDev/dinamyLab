public function testFacturaElectronicaContieneCamposObligatorios()
{
    $factura = new FacturaElectronica();
    $xml = $factura->generarXML();

    $this->assertStringContainsString('<cbc:CustomizationID>SS-CUFE</cbc:CustomizationID>', $xml);
    $this->assertStringContainsString('<cac:AccountingSupplierParty>', $xml);
    $this->assertStringContainsString('<cac:AccountingCustomerParty>', $xml);
}
