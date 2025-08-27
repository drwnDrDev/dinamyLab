import React, { useState, useMemo } from 'react';
import { Head } from '@inertiajs/react';
import Layout from '@/Layouts/AuthenticatedLayout';

export default function Index({ diagnosticos }) {
    const [searchTerm, setSearchTerm] = useState('');
    const [loading, setLoading] = useState(false);
    const [currentPage, setCurrentPage] = useState(1);
    const [updatingId, setUpdatingId] = useState(null);
    const itemsPerPage = 20;

    // Filtrar diagnósticos
    const filteredDiagnosticos = useMemo(() => {
        if (!searchTerm) return diagnosticos;

        return diagnosticos.filter((cie) => {
            const searchLower = searchTerm.toLowerCase();
            return (
                cie.descripcion?.toLowerCase().includes(searchLower) ||
                cie.codigo?.toLowerCase().includes(searchLower) ||
                cie.nombre?.toLowerCase().includes(searchLower) ||
                cie.grupo?.toLowerCase().includes(searchLower)
            );
        });
    }, [diagnosticos, searchTerm]);

    // Calcular paginación
    const paginatedDiagnosticos = useMemo(() => {
        const startIndex = (currentPage - 1) * itemsPerPage;
        return filteredDiagnosticos.slice(startIndex, startIndex + itemsPerPage);
    }, [filteredDiagnosticos, currentPage]);

    // Calcular total de páginas
    const totalPages = Math.ceil(filteredDiagnosticos.length / itemsPerPage);

    // Manejadores
    const handleSearch = (e) => {
        setLoading(true);
        setSearchTerm(e.target.value);
        setCurrentPage(1); // Resetear a primera página al buscar
        setTimeout(() => setLoading(false), 300);
    };

    const handlePageChange = (pageNumber) => {
        setCurrentPage(pageNumber);
    };

    // Agregar función para actualizar estado
    const handleToggleStatus = async (cie) => {
        try {
            setUpdatingId(cie.id);
            
            const response = await fetch(`/api/codigo-diagnostico/${cie.id}/toggle-status`, {
                method: 'GET',

             
            });
            console.log('Response status:', response.status);

            if (!response.ok) {
                throw new Error('Error al actualizar el estado');
                console.log('Response status:', response.status);
            }

            // Actualizar estado en el frontend
            const updatedDiagnosticos = diagnosticos.map(d => {
                if (d.id === cie.id) {
                    return { ...d, activo: !d.activo };
                }
                return d;
            });

            diagnosticos = updatedDiagnosticos;

        } catch (error) {
            console.error('Error:', error);
            alert('No se pudo actualizar el estado');
        } finally {
            setUpdatingId(null);
        }
    };

    // Componente de Paginación
    const Pagination = () => {
        const pages = [];
        for (let i = 1; i <= totalPages; i++) {
            pages.push(
                <button
                    key={i}
                    onClick={() => handlePageChange(i)}
                    className={`px-2 py-1 rounded-md ${
                        currentPage === i 
                            ? 'bg-primary text-white' 
                            : 'bg-secondary hover:bg-primary/10'
                    }`}
                >
                    {i}
                </button>
            );
        }

        return (
            <div className="flex flex-col gap-2 items-center justify-between p-4 bg-background">
                
                <div className="flex items-center gap-1">
                    <button
                        onClick={() => handlePageChange(currentPage - 1)}
                        disabled={currentPage === 1}
                        className="px-3 py-1.5 rounded-md bg-secondary hover:bg-primary/10 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium"
                    >
                        Anterior
                    </button>
                    <div className="flex gap-1 mx-2 max-w-xs overflow-x-auto">
                        {pages}
                    </div>
                    <button
                        onClick={() => handlePageChange(currentPage + 1)}
                        disabled={currentPage === totalPages}
                        className="px-3 py-1.5 rounded-md bg-secondary hover:bg-primary/10 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium"
                    >
                        Siguiente
                    </button>
                </div>
                <div className="flex items-center gap-2">
                    <span className="text-sm text-titles">
                        Mostrando {((currentPage - 1) * itemsPerPage) + 1} a {Math.min(currentPage * itemsPerPage, filteredDiagnosticos.length)} de {filteredDiagnosticos.length} resultados
                    </span>
                </div>
            </div>
        );
    };

    return (
        <>
            <Head title="CIE-10 Codes" />
            
            <Layout>
                <div className="py-12">
                    <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <section className="flex flex-wrap items-center justify-between mb-4">
                            <h2 className="font-semibold text-xl text-text leading-tight">
                                Codigos CIE-10 ({filteredDiagnosticos.length})
                            </h2>

                            <div className="py-3 w-1/2 mx-auto">
                                <label className="flex flex-col min-w-40 h-12 w-full">
                                    <div className="flex w-full flex-1 items-stretch rounded-xl h-full">
                                        <div className="text-titles flex border-none bg-secondary items-center justify-center pl-4 rounded-l-xl border-r-0">
                                            {loading ? (
                                                <div className="animate-spin h-5 w-5 border-2 border-primary border-t-transparent rounded-full" />
                                            ) : (
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                                    <path d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"></path>
                                                </svg>
                                            )}
                                        </div>
                                        <input
                                            type="text"
                                            placeholder="Buscar por descripción o código"
                                            className="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-text focus:outline-0 focus:ring-0 border-none bg-secondary focus:border-none h-full placeholder:text-titles px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal"
                                            value={searchTerm}
                                            onChange={handleSearch}
                                        />
                                    </div>
                                </label>
                            </div>
                        </section>

                        <div className="my-3 overflow-hidden rounded-xl border border-borders bg-background">
                            <div className="overflow-x-auto">
                                <table className="w-full">
                                    <thead>
                                        <tr className="bg-background">
                                            <th className="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Descripción</th>
                                            <th className="px-4 py-3 text-left text-text w-32 text-sm font-medium leading-normal">Código</th>
                                            <th className="px-4 py-3 text-left text-text w-96 text-sm font-medium leading-normal">Grupo</th>
                                            <th className="px-4 py-3 text-left text-text w-60 text-sm font-medium leading-normal">Nombre</th>
                                            <th className="px-4 py-3 text-left text-text w-60 text-sm font-medium leading-normal">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {paginatedDiagnosticos.map((cie) => (
                                            <tr 
                                                key={cie.id}
                                                className="border-t border-borders cursor-pointer hover:bg-secondary"
                                                onDoubleClick={() => handleToggleStatus(cie)}
                                            >
                                                <td className="content-start px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
                                                    {cie.descripcion}
                                                </td>
                                                <td className="content-start px-4 py-2 w-32 text-titles text-sm font-normal leading-normal">
                                                    {cie.codigo}
                                                </td>
                                                <td className="content-start px-4 py-2 w-96 text-sm font-normal leading-normal">
                                                    {cie.grupo}
                                                </td>
                                                <td className="content-start px-4 py-2 w-96 text-sm font-normal leading-normal">
                                                    {cie.nombre}
                                                </td>
                                                <td className="content-start px-4 py-2 w-60 text-titles text-sm font-normal leading-normal">
                                                    {updatingId === cie.id ? (
                                                        <span className="inline-flex items-center">
                                                            <svg className="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24">
                                                                <circle 
                                                                    className="opacity-25" 
                                                                    cx="12" 
                                                                    cy="12" 
                                                                    r="10" 
                                                                    stroke="currentColor" 
                                                                    strokeWidth="4"
                                                                />
                                                                <path 
                                                                    className="opacity-75" 
                                                                    fill="currentColor" 
                                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                                />
                                                            </svg>
                                                            Actualizando...
                                                        </span>
                                                    ) : (
                                                        <span 
                                                            className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                                ${cie.activo 
                                                                    ? 'bg-green-100 text-green-800' 
                                                                    : 'bg-red-100 text-red-800'
                                                                }`}
                                                            title="Doble click para cambiar estado"
                                                        >
                                                            {cie.activo ? 'Activo' : 'Inactivo'}
                                                        </span>
                                                    )}
                                                </td>
                                            </tr>
                                        ))}
                                        {filteredDiagnosticos.length === 0 && (
                                            <tr>
                                                <td colSpan="4" className="text-center py-4 text-gray-500">
                                                    No se encontraron resultados para "{searchTerm}"
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                            {filteredDiagnosticos.length > 0 && (
                                <div className="border-t border-borders">
                                    <Pagination />
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </Layout>
        </>
    );
}