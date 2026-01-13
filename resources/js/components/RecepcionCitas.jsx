import React, { useState } from 'react';
import RecepcionPreRegistros from './RecepcionPreRegistros';
import ConfirmarPreRegistro from './ConfirmarPreRegistro';

/**
 * Componente principal de recepción que integra:
 * - Lista de pre-registros pendientes
 * - Modal/vista para confirmar y completar registro
 *
 * REQUIERE:
 * - Autenticación (personal de recepción)
 * - FormPersona como prop para completar registros
 */
const RecepcionCitas = ({ FormPersona }) => {
    const [vistaActual, setVistaActual] = useState('lista'); // 'lista' | 'confirmar'
    const [preRegistroSeleccionado, setPreRegistroSeleccionado] = useState(null);

    const handleSeleccionarParaConfirmar = (preRegistro) => {
        setPreRegistroSeleccionado(preRegistro);
        setVistaActual('confirmar');
    };

    const handleVolverALista = () => {
        setVistaActual('lista');
        setPreRegistroSeleccionado(null);
    };

    const handleConfirmacionExitosa = (data) => {
        console.log('Confirmación exitosa:', data);

        // Mostrar notificación de éxito
        alert(`✅ Registro confirmado exitosamente!\n\nPersona ID: ${data.data.persona_id}\nCódigo: ${data.data.codigo_confirmacion}`);

        // Volver a la lista
        handleVolverALista();
    };

    return (
        <div className="min-h-screen bg-gray-50">
            {vistaActual === 'lista' && (
                <RecepcionPreRegistros
                    onSeleccionarParaConfirmar={handleSeleccionarParaConfirmar}
                />
            )}

            {vistaActual === 'confirmar' && (
                <div className="py-6">
                    <ConfirmarPreRegistro
                        preRegistro={preRegistroSeleccionado}
                        FormPersona={FormPersona}
                        onSuccess={handleConfirmacionExitosa}
                        onCancel={handleVolverALista}
                    />
                </div>
            )}
        </div>
    );
};

export default RecepcionCitas;
