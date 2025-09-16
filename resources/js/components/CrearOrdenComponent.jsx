import React, { useState, useEffect } from 'react';

const CrearOrdenComponent = () => {
    const [data, setData] = useState({});

    return (
        <div className="border p-4 rounded-lg">
            <h2 className="text-xl font-bold mb-2">Componente de React</h2>
            <p>Este es un componente de React renderizado dentro de una vista de Blade.</p>
            <p className="mt-4">Ejemplo de estado:</p>
            <button 
                className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                onClick={() => setCount(count + 1)}
            >
                Has hecho click {count} veces
            </button>
        </div>
    );
};

export default CrearOrdenComponent;
