import React from 'react';
import { createRoot } from 'react-dom/client';
import ComponenteEjemplo from './components/ComponenteEjemplo';

document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('react-ejemplo');
  if (container) {
    const props = JSON.parse(container.dataset.props || '{}');
    createRoot(container).render(<ComponenteEjemplo {...props} />);
  }
});
