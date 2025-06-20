# 📄 Campos de Factura Electrónica en Salud (Resolución 2275 de 2023)

## 1. Encabezado
- Tipo de documento (Factura electrónica de venta)
- Indicador de validez fiscal
- Número de documento (XML UBL)
- Fecha de emisión, validación y vencimiento
- Código QR / CUFE
- **Invoice/cbc:CustomizationID** → Indica tipo de operación en salud:
  - `SS-CUFE`, `SS-Recaudo`, `SS-Reporte`, `SS-SinAporte` :contentReference[oaicite:1]{index=1}

## 2. Datos del Emisor (Prestador o proveedor de tecnología)
- Razón social / Nombre, NIT
- Actividad económica
- Responsabilidad fiscal
- Ciudad, dirección, teléfono, correo de contacto

## 3. Datos del Adquiriente (EPS, ARL, SOAT, ADRES, etc.)
- Razón social / Nombre, NIT o ID
- Actividad económica, dirección, teléfono, correo electrónico

## 4. Detalle de Productos/Servicios
- Código, descripción, cantidad, unidad de medida
- Valor unitario, porcentaje impuesto, impuesto, descuento
- Valor total por ítem

## 5. Extensión Salud (Anexo Técnico 2)
- Campos vinculados con RIPS vía XML UBL extension :contentReference[oaicite:2]{index=2}:
  - Datos de la transacción
  - Identificación del usuario
  - Detalle asistencial: consultas, procedimientos, urgencias, hospitalización, recién nacidos, medicamentos, otros servicios

## 6. Totales y Tributos
- Subtotal, base imponible, IVA, ICA, INC, IC
- Otros cargos (bolsas, recargos), total facturado, monto en letras

## 7. Impuestos y Retenciones
- Tipo de impuesto, base, valor
- Tipo de retención, porcentaje y total retenido

## 8. RIPS (Registro como JSON)
- Archivo JSON del RIPS soporte de la factura, nota débito/crédito, o nota ajuste :contentReference[oaicite:3]{index=3}
- Nota ajuste: sin documento electrónico si no afecta valor
- RIPS sin factura para servicios no monetizados

## 9. Validación y envío
- Validación previa DIAN (XML), luego validación única del RIPS (Ministerio)
- Transmisión a través del Mecanismo Único de Validación (API/web service)
- Emisión de CUV
- Radicación ante pagador en máximo 22 días hábiles :contentReference[oaicite:4]{index=4}

## 10. Medio de Pago
- Tipo de medio (crédito, efectivo, transferencia), fecha de vencimiento
- Referencia, banco, canal

## 11. Información adicional y pie de página
- Observaciones / notas
- CUFE, software de facturación, versión, página

---

### 📌 Notas Clave:
- La extensión “**CustomizationID**” identifica el tipo de operación en el sector salud :contentReference[oaicite:5]{index=5}.
- El **anexo técnico 2** de la Resolución define campos adicionales en XML vinculados al RIPS :contentReference[oaicite:6]{index=6}.
- El RIPS en formato JSON es obligatorio como soporte; en caso de nota ajuste o servicios sin recaudo, aplica la presentación sin documento electrónico :contentReference[oaicite:7]{index=7}.

---

¿Deseas que genere el archivo `.md` actualizado con este contenido?
::contentReference[oaicite:8]{index=8}


## 4. Detalle de Productos/Servicios
- Código del servicio (estándar SOAT o CUPS)
- Cantidad
- Unidad de medida
- **Descripción genérica del servicio** (Ej: "Consulta médica general", "Servicio de laboratorio")
- Valor unitario
- % de impuesto
- Impuesto
- Descuento
- Valor total por ítem
