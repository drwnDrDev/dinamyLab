import React from 'react';
import { createRoot } from 'react-dom/client';



import Setup from './components/setup/Setup';
import TestComponent from './components/TestComponent';
import '../css/app.css';
import DropdownComponent from './components/DropdownComponent';
import Typewriter from './components/guest/Typewriter';
import TarifasExamenesSede from './components/TarifasExamenesSede';
import ResultadosLote from './components/resultados/ResultadosLote';
import { toast } from './lib/toast';
import { mountToasts } from './lib/toastDom';

// Toasts: flash de Blade y window.toast() para el JS de las vistas.
window.toast = toast;
mountToasts();

// Renderizado condicional basado en la presencia de elementos en el DOM



if (document.getElementById('dropdownComponent')) {
    const root = createRoot(document.getElementById('dropdownComponent'));
    const nombre = document.getElementById('dropdownComponent').getAttribute('data-nombre') || 'nombre';
    const profileUrl = document.getElementById('dropdownComponent').getAttribute('data-profile-url') || '/profile';
    root.render(
        <DropdownComponent
            name={nombre}
            profileUrl={profileUrl}
        />
    );
}
if (document.getElementById('typewriter')) {
    const element = document.getElementById('typewriter');
    const text = element.getAttribute('text') || 'Your trusted partner in clinical laboratory management';
    const root = createRoot(element);
    root.render(<Typewriter text={text} typingSpeed={50} />);
}
if (document.getElementById('testComponent')) {
    const element = document.getElementById('testComponent');
    const loginUser = element.getAttribute('data-login-user');
    const registerUser = element.getAttribute('data-register-user');
    const authUser = JSON.parse(element.getAttribute('data-auth-user'));

    const root = createRoot(element);
    root.render(<TestComponent loginUser={loginUser} registerUser={registerUser} authUser={authUser} />);
}

if (document.getElementById('root')) {
    const root = createRoot(document.getElementById('root'));
    root.render(<Setup />);
}

if (document.getElementById('tarifas-examenes-root')) {
    const element = document.getElementById('tarifas-examenes-root');
    const sedeId = parseInt(element.getAttribute('data-sede-id') || '0', 10);
    const root = createRoot(element);
    root.render(<TarifasExamenesSede sedeId={sedeId} />);
}

if (document.getElementById('resultados-lote-root')) {
    const element = document.getElementById('resultados-lote-root');
    const initialMuestras = JSON.parse(element.querySelector('#resultados-lote-data').textContent);
    const root = createRoot(element);
    root.render(
        <ResultadosLote
            action={element.dataset.action}
            initialMuestras={initialMuestras}
        />
    );
}

//
