import React from 'react';

/**
 * Sección de formulario con título discreto y grid de 12 columnas en md+.
 * Los hijos definen su ancho con `md:col-span-*`; en móvil se apilan.
 */
const FormSection = ({ title, children, className = '' }) => (
    <section className={className}>
        {title && (
            <h3 className="flex items-center gap-3 mb-2 text-xs font-semibold uppercase tracking-wide text-titles">
                {title}
                <span className="flex-1 h-px bg-secondary" aria-hidden="true" />
            </h3>
        )}
        <div className="grid grid-cols-1 md:grid-cols-12 gap-x-4 gap-y-3">
            {children}
        </div>
    </section>
);

export default FormSection;
