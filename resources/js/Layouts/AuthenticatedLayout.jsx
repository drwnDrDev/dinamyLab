// filepath: c:\CURSOS DEV\PROJECTS\DinamyLAB\resources\js\Layouts\AuthenticatedLayout.jsx
import React from 'react';

export default function AuthenticatedLayout({ children }) {
    return (
        <div className="min-h-screen bg-gray-100">
            <nav className="bg-white border-b border-gray-100">
                {/* Aquí puedes poner tu navegación existente */}
            </nav>

            <main>{children}</main>
        </div>
    );
}