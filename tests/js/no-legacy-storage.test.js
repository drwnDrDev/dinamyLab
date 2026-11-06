import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
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

test('los consumidores usan el módulo de catálogos, no localStorage', async () => {
    for (const relativo of consumidores) {
        const fuente = await readFile(path.join(root, relativo), 'utf8');
        assert.equal(fuente.includes('localStorage'), false, `${relativo} no debe usar localStorage`);
        assert.equal(fuente.includes("_data'"), false, `${relativo} no debe usar claves *_data`);
    }
});
