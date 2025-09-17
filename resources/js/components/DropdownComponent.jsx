import { useState } from 'react';
import React from 'react';

export default function DropdownComponent(props) {
    const [verOpciones, setVerOpciones] = useState(false);


    const toggleDropdown = () => {
        setVerOpciones(prev => !prev);
    };
    console.log(props);
    return (
        <div className="relative inline-block text-left">
            <div>
                <button type="button"
                className="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500"
                id="menu-button"
                aria-expanded={verOpciones ? "true" : "false"}
                aria-haspopup="true"
                onClick={toggleDropdown}>
                    Options
                    <svg className="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fillRule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.584l3.71-4.354a.75.75 0 111.14.976l-4.25 5a.75.75 0 01-1.14 0l-4.25-5a.75.75 0 01.02-1.06z" clipRule="evenodd" />
                    </svg>
                </button>
            </div>

            {verOpciones ? (
                <div className="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabIndex="-1">
                    <div className="py-1" role="none">
                        <a href="#" className="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem" tabIndex="-1" id="menu-item-0">{props.name}</a>
                        <a href="#" className="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem" tabIndex="-1" id="menu-item-1">{props.support}</a>
                        <a href="#" className="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem" tabIndex="-1" id="menu-item-2">{props.license}</a>
                        <form method="POST" action="#" role="none">
                            <button type="submit" className="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100" role="menuitem" tabIndex="-1" id="menu-item-3">{props.signOut}</button>
                        </form>
                    </div>
                </div>
            ) : null}
        </div>
    );
}
