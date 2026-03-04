import React, { useState, useEffect, useRef } from 'react';
import useAxiosAuth from './hooks/useAxiosAuth';

const RECENT_KEY = 'default_cies_data';
const MAX_RECENT = 10;
const MIN_CHARS = 3;
const DEBOUNCE_MS = 300;

function getStoredRecents() {
    try { return JSON.parse(localStorage.getItem(RECENT_KEY) || '[]'); }
    catch { return []; }
}

function pushToRecents(item) {
    const list = getStoredRecents().filter(r => r.codigo !== item.codigo);
    localStorage.setItem(RECENT_KEY, JSON.stringify([item, ...list].slice(0, MAX_RECENT)));
}

const Spinner = () => (
    <svg className="animate-spin w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24">
        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
    </svg>
);

const CieItem = ({ item, onSelect }) => (
    <button
        type="button"
        className="w-full text-left px-3 py-2 hover:bg-secondary flex items-center gap-0"
        onMouseDown={e => { e.preventDefault(); onSelect(item); }}
    >
        <span className="font-mono text-xs text-gray-400 w-10 shrink-0">{item.codigo}</span>
        <span className="text-sm text-text capitalize truncate">{item.nombre}</span>
    </button>
);

const CieSearchInput = ({ label, name, value, onChange, error }) => {
    const { axiosInstance } = useAxiosAuth();
    const [inputText, setInputText] = useState('');
    const [searchQuery, setSearchQuery] = useState(null);
    const [selected, setSelected] = useState(null);
    const [isOpen, setIsOpen] = useState(false);
    const [results, setResults] = useState([]);
    const [loading, setLoading] = useState(false);
    
    const [recents, setRecents] = useState([]);
    const wrapperRef = useRef(null);
    const inputRef = useRef(null);

    // Cargar recientes al montar
    useEffect(() => setRecents(getStoredRecents()), []);

    // Sincronizar display cuando cambia el value prop externamente
    useEffect(() => {
        if (!value) {
            setSelected(null);
            setInputText('');
            return;
        }
        if (selected && String(selected.codigo) === String(value)) return;

        // Buscar en recientes primero (sin llamada API)
        const fromRecents = getStoredRecents().find(r => String(r.codigo) === String(value));
        if (fromRecents) {
            setSelected(fromRecents);
            setInputText(fromRecents.nombre);
            return;
        }

        // Obtener nombre desde API
        axiosInstance
            .post(`/api/cie10/buscar`, { query: value })
            .then(res => {
                const data = Array.isArray(res.data) ? res.data : [];
                const match = data.find(item => String(item.codigo) === String(value));
                if (match) { setSelected(match); setInputText(match.nombre); }
            })
            .catch(() => {});
    }, [value]); // eslint-disable-line react-hooks/exhaustive-deps

    
    // Búsqueda con debounce al escribir
    useEffect(() => {
        if (!searchQuery || searchQuery.length < MIN_CHARS) {
            setResults([]);
            setLoading(false);
            return;
        }
        
        setLoading(true);
        const timer = setTimeout(() => {
            axiosInstance
                .post(`/api/cie10/buscar`, { query: searchQuery })
                .then(res => {
                    setResults(Array.isArray(res.data?.data) ? res.data.data.slice(0, 10) : []);
                })
                .catch(() => setResults([]))
                .finally(() => setLoading(false));
        }, DEBOUNCE_MS);
        return () => clearTimeout(timer);
    }, [searchQuery]); // eslint-disable-line react-hooks/exhaustive-deps

    // Cerrar dropdown al hacer click afuera y restaurar nombre seleccionado
    useEffect(() => {
        const handler = e => {
            if (wrapperRef.current && !wrapperRef.current.contains(e.target)) {
                setIsOpen(false);
                setInputText(selected ? selected.nombre : '');
            }
        };
        document.addEventListener('mousedown', handler);
        return () => document.removeEventListener('mousedown', handler);
    }, [selected]);

    const handleFocus = () => {
        setRecents(getStoredRecents());
        setIsOpen(true);
    };

    const handleChange = e => {
        setInputText(e.target.value);
        setSearchQuery(e.target.value || null);
        setIsOpen(true);
    };

    const handleSelect = item => {
        setSelected(item);
        setInputText(item.nombre);
        setSearchQuery(null);
        setResults([]);
        setIsOpen(false);
        pushToRecents(item);
        setRecents(getStoredRecents());
        onChange({ target: { name, value: item.codigo } });
    };

    const handleClear = () => {
        setSelected(null);
        setInputText('');
        setSearchQuery(null);
        setResults([]);
        onChange({ target: { name, value: '' } });
        inputRef.current?.focus();
    };

    const showRecents = isOpen && inputText.length < MIN_CHARS && recents.length > 0;
    const showResults = isOpen && inputText.length >= MIN_CHARS;
    const dropdownOpen = showRecents || showResults;

    const ringCls = error
        ? 'ring-1 ring-red-400 focus-within:ring-2 focus-within:ring-red-500'
        : 'ring-1 ring-borders focus-within:ring-2 focus-within:ring-primary';

    return (
        <div ref={wrapperRef} className="relative">
            <label htmlFor={name} className="block font-medium text-sm text-text mb-1">
                {label}
            </label>

            <div className={`flex items-stretch rounded-md overflow-hidden transition-colors bg-white ${ringCls}`}>
                {/* Prefix: código CIE activo */}
                <span className="flex items-center justify-center px-2.5 min-w-[2.75rem] bg-secondary border-r border-borders text-xs font-mono font-semibold text-titles shrink-0 select-none">
                    {selected?.codigo ?? '—'}
                </span>

                {/* Input de búsqueda */}
                <input
                    ref={inputRef}
                    id={name}
                    name={name}
                    type="text"
                    autoComplete="off"
                    placeholder="Buscar CIE10..."
                    className="flex-1 w-full h-9 px-2.5 text-sm text-text bg-transparent border-none focus:ring-0 min-w-0 placeholder:text-gray-300"
                    value={inputText}
                    onChange={handleChange}
                    onFocus={handleFocus}
                />

                {/* Spinner o botón limpiar */}
                <span className="flex items-center pr-2.5 shrink-0">
                    {loading
                        ? <Spinner />
                        : selected && (
                            <button
                                type="button"
                                tabIndex={-1}
                                onClick={handleClear}
                                className="text-gray-400 hover:text-gray-600 focus:outline-none"
                            >
                                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        )
                    }
                </span>
            </div>

            {/* Dropdown */}
            {dropdownOpen && (
                <div className="absolute z-50 w-full mt-1 bg-white border border-borders rounded-md shadow-sm max-h-60 overflow-y-auto">
                    {showRecents && (
                        <>
                            <div className="px-3 py-1.5 text-xs text-gray-400 uppercase tracking-wide border-b border-borders">
                                Recientes
                            </div>
                            {recents.map(item => (
                                <CieItem key={item.codigo} item={item} onSelect={handleSelect} />
                            ))}
                        </>
                    )}
                    {showResults && !loading && results.length === 0 && (
                        <p className="px-3 py-2 text-sm text-gray-400">Sin resultados</p>
                    )}
                    {showResults && results.map(item => (
                        <CieItem key={item.codigo} item={item} onSelect={handleSelect} />
                    ))}
                </div>
            )}

            {error && <p className="text-sm text-red-500 mt-1">{error}</p>}
        </div>
    );
};

export default CieSearchInput;
