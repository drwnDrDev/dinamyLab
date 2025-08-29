import React from 'react';
import { createRoot } from 'react-dom/client';

function Test() {
  return <div style={{ background: 'lightgreen', padding: '1rem' }}>React está funcionando 🎉</div>;
}

const container = document.getElementById('react-root');
if (container) {
  createRoot(container).render(<Test />);
}
