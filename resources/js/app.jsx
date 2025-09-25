import React from 'react';
import { createRoot } from 'react-dom/client';



import Setup from './components/setup/Setup';
import TestComponent from './components/TestComponent';
import '../css/app.css';
import DropdownComponent from './components/DropdownComponent';
import Typewriter from './components/guest/Typewriter';

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

//
