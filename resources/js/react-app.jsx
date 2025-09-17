import React from 'react';
import { createRoot } from 'react-dom/client';
import { useState } from 'react';
import CrearOrdenComponent from './components/CrearOrdenComponent';

if (document.getElementById('react-crear-orden')) {
    const element = document.getElementById('react-crear-orden');
    const root = createRoot(element);
    root.render(<CrearOrdenComponent />);
}
