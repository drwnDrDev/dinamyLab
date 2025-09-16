import './bootstrap';
import React from 'react';
import Alpine from 'alpinejs';
import { createRoot } from 'react-dom/client';
function TestComponent() {
    return React.createElement('div', null, [
        React.createElement('h1', null, 'React Test Component'),
        React.createElement('button', { 
            className: 'btn btn-primary',
            onClick: () => alert('React is working!')
        }, 'Click Me')
    ]);
}

const container = document.getElementById('root');
if (container) {
    const root = createRoot(container);
    root.render(React.createElement(TestComponent));
}


window.Alpine = Alpine;

Alpine.start();
