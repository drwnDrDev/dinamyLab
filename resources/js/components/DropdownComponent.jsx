
import { useState } from 'react';
import React from 'react';
import axios from 'axios';

export default function DropdownComponent(props) {
    const [verOpciones, setVerOpciones] = useState(false);
    const [sedes, setSedes] = useState([]);

    React.useEffect(() => {
        // Parsear las sedes desde el atributo data
        const sedesData = document.getElementById('dropdownComponent')?.getAttribute('data-sedes');
        if (sedesData) {
            try {
                setSedes(JSON.parse(sedesData));
            } catch (e) {
                console.error('Error parsing sedes:', e);
            }
        }
    }, []);

    const toggleDropdown = () => {
        setVerOpciones(prev => !prev);
    };

    const handleClickOutside = (event) => {
        if (!event.target.closest('.relative')) {
            setVerOpciones(false);
        }
    };


    return (
        <div className="relative inline-block text-left">
            <div>
                <button type="button"
                    className="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500"
                    id="menu-button"
                    aria-expanded={verOpciones ? "true" : "false"}
                    aria-haspopup="true"
                    onClick={() => {
                        toggleDropdown();
                        document.addEventListener('click', handleClickOutside);
                    }}>
                    {props.name}
                    <svg className="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fillRule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.584l3.71-4.354a.75.75 0 111.14.976l-4.25 5a.75.75 0 01-1.14 0l-4.25-5a.75.75 0 01.02-1.06z" clipRule="evenodd" />
                    </svg>
                </button>
            </div>

            {verOpciones ? (
                <div className="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabIndex="-1">
                    <div className="py-1" role="none">
                        <a href={props.profileUrl} className="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem" tabIndex="-1" id="menu-item-0">Perfil</a>

                        {sedes.length > 0 && (
                            <>
                                <div className="border-t border-gray-100"></div>
                                <div className="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Sedes</div>
                                {sedes.map((sede, index) => (
                                    <a
                                        key={sede.id}
                                        href={sede.url}
                                        className="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 flex items-center gap-2"
                                        role="menuitem"
                                        tabIndex="-1"
                                    >
                                        {sede.logo && (
                                            <img
                                                src={`/storage/logos/${sede.logo}`}
                                                alt={sede.nombre}
                                                className="w-5 h-5 rounded-full"
                                            />
                                        )}
                                        <span>{sede.nombre}</span>
                                    </a>
                                ))}
                            </>
                        )}

                        <div className="border-t border-gray-100"></div>
                        <button
                            className="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                            role="menuitem"
                            tabIndex="-1"
                            id="menu-item-3"
                            onClick={() => {
                                axios.post('/logout').then(() => {
                                    window.location.href = '/login';
                                });
                            }}
                        >
                            Cerrar sesión</button>

                    </div>
                </div>
            ) : null}
        </div>
    );
}
