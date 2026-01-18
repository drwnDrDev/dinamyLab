import React from "react";
import { createRoot } from "react-dom/client";
import FormPersona from "./components/FormPersona";

if (document.getElementById("react-create-persona")) {
    const element = document.getElementById("react-create-persona");
    const root = createRoot(element);
    root.render(<FormPersona perfil="Paciente"/>);
}
