import test from 'node:test';
import assert from 'node:assert/strict';
import { access, readFile } from 'node:fs/promises';
import path from 'node:path';

const root = process.cwd();

const consumidores = [
    'resources/js/components/hooks/usePersonaReferencias.jsx',
    'resources/js/components/hooks/useTablasRef.jsx',
    'resources/js/components/ConvenioForm.jsx',
    'resources/js/convenioCreate.jsx',
    'resources/js/components/setup/Setup.jsx',
    'resources/js/components/CieSearchInput.jsx',
];

const eliminados = [
    'resources/js/obtenerStaticos.js',
    'resources/js/main.js',
    'resources/js/manejadorEventos.js',
    'resources/js/crearExamenes.js',
    'resources/js/variables.js',
    'resources/js/api.js',
    'resources/js/formularioPersona.js',
    'resources/js/crearOrdenes.js',
    'resources/js/crearPersona.js',
    'resources/js/buscarPersona.js',
    'resources/js/resultado.js',
    'resources/js/setup.js',
    'resources/js/components/hooks/useCieCups.jsx',
    'resources/js/utils/paisesLocalStorage.js',
];

test('los consumidores usan el módulo de catálogos, no localStorage', async () => {
    for (const relativo of consumidores) {
        const fuente = await readFile(path.join(root, relativo), 'utf8');
        assert.equal(fuente.includes('localStorage'), false, `${relativo} no debe usar localStorage`);
        assert.equal(fuente.includes("_data'"), false, `${relativo} no debe usar claves *_data`);
    }
});

test('los módulos legacy fueron retirados', async () => {
    for (const relativo of eliminados) {
        await assert.rejects(access(path.join(root, relativo)), `${relativo} no debe existir`);
    }
});

test('vite no incluye entrypoints muertos', async () => {
    const vite = await readFile(path.join(root, 'vite.config.js'), 'utf8');

    for (const entry of ['main.js', 'crearOrdenes.js', 'obtenerStaticos.js', 'crearPersona.js', 'buscarPersona.js']) {
        assert.equal(vite.includes(entry), false, `vite.config.js no debe incluir ${entry}`);
    }
});
