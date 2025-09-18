import React, { useState } from 'react';
import { createRoot } from 'react-dom/client';

function CrearOrdenComponent() {
    const [data, setData] = useState({});
    const [count, setCount] = useState(0);

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
}

const container = document.getElementById('react-crear-orden');
if (container) {
    const root = createRoot(container);
    root.render(
        <React.StrictMode>
            <CrearOrdenComponent />
        </React.StrictMode>
    );
}
