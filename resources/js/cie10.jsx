import React from 'react'
import { createRoot } from 'react-dom/client'
import Cie10Table from './components/Cie10Table'

if (document.getElementById('cie10-root')) {
    const root = createRoot(document.getElementById('cie10-root'))
    root.render(
        <React.StrictMode>
            <Cie10Table />
        </React.StrictMode>
    )
}