 ¿Quiénes sí practican retención en la fuente?
Según el Estatuto Tributario (ET) y la Resolución 000042 de 2020, las siguientes personas o entidades sí deben practicar retención al pagar servicios:

Tipo de pagador	¿Retiene?	Observaciones
EPS, IPS, ARL, ADRES	✅ Sí	Son grandes contribuyentes o agentes retenedores
Personas jurídicas (SAS, LTDA, etc.)	✅ Sí	Si están inscritas como agentes de retención
Entidades del Estado	✅ Sí	Todas las entidades oficiales retienen
Comerciantes o empresas grandes	✅ Sí	Según su actividad y clasificación tributaria

❌ ¿Quiénes no deben retener?
Tipo de pagador	¿Retiene?	Observaciones
Persona natural no comerciante	❌ No	Ej: un paciente común
Persona natural que no es agente retenedor	❌ No	Aunque tenga RUT, si no está obligada, no retiene
Personas naturales del régimen simple	❌ No	Régimen SIMPLE no practica retención
Entidades pequeñas que no cumplen condiciones del Art. 368-2 ET	❌ No	Por no ser "grandes contribuyentes" ni designadas como retenedoras

🧠 ¿Cómo saber si alguien es agente de retención?
En el RUT del cliente (adquirente/pagador), aparece el código de responsabilidad:

"22 - Agente de retención en la fuente a título de renta" → ✅ Retiene

Si no aparece → ❌ No retiene

📌 Recomendación para tu sistema Laravel
Cuando vayas a generar la factura, puedes usar esta lógica:

php
Copiar
Editar
if ($cliente->es_agente_retencion) {
    $retencion = $subtotal * 0.015; // o la tarifa aplicable
} else {
    $retencion = 0;
}
Donde es_agente_retencion puede evaluarse a partir de:

El campo responsabilidades_tributarias en la tabla de clientes

Consulta al RUT (si se automatiza con la DIAN)

🧾 Ejemplo:
Factura a EPS Vida Sana (persona jurídica, gran contribuyente):

✔️ Aplica retefuente (1.5% o según tarifa)

Factura a Juan Pérez (paciente natural, no comerciante):

❌ No aplica retefuente

