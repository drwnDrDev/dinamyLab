import React from 'react';
import { createRoot } from 'react-dom/client';
import CrearOrdenComponent from './components/CrearOrdenComponent';

const container = document.getElementById('react-crear-orden');
if (container) {
    const root = createRoot(container);
    root.render(
        <React.StrictMode>
            <CrearOrdenComponent />
        </React.StrictMode>
    );
}
