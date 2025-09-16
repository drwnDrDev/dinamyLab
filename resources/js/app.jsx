
import React from 'react';
import { createRoot } from 'react-dom/client';


import TestComponent from './components/TestComponent';


if (document.getElementById('root')) {
    const root = createRoot(document.getElementById('root'));
    console.log(document.getElementById('react-test-component'));       
    root.render(<TestComponent />);
}

//