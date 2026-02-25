import React, { useState, useEffect } from 'react';
import AutocompleteInput from './AutocompleteInput';

export default function ConvenioForm({ documentos = [], onSubmit = null }) {
  const [formData, setFormData] = useState({
    tipo_documento: documentos.length > 0 ? documentos[0]?.cod_dian : '91',
    numero_documento: '',
    razon_social: '',
    telefono: '',
    correo: '',
    municipio: '11001',
    direccion: '',
    pais: '',
    redes: {
      whatsapp: '',
      maps: '',
      linkedin: '',
      facebook: '',
      instagram: '',
      tiktok: '',
      youtube: '',
      website: '',
      otras_redes: '',
    },
  });

  const [paises, setPaises] = useState([]);
  const [paisesEn, setPaisesEn] = useState('es');

  // Cargar países del localStorage al montar el componente
  useEffect(() => {
    const paisesLocal = localStorage.getItem('paises');
    if (paisesLocal) {
      try {
        const paisesData = JSON.parse(paisesLocal);
        setPaises(paisesData);
      } catch (error) {
        console.error('Error al parsear países del localStorage:', error);
        setPaises([]);
      }
    }
  }, []);

  // Manejar cambios en campos simples
  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  // Manejar cambios en campos de redes sociales
  const handleRedesChange = (e) => {
    const { name, value } = e.target;
    const fieldName = name.replace('redes[', '').replace(']', '');
    setFormData((prev) => ({
      ...prev,
      redes: {
        ...prev.redes,
        [fieldName]: value,
      },
    }));
  };

  // Manejar cambio en el campo País con autocomplete
  const handlePaisChange = (value) => {
    setFormData((prev) => ({
      ...prev,
      pais: value,
    }));
  };

  // Obtener lista de países para el autocomplete
  const paisesFiltr = paises
    .filter((p) => p.toLowerCase().includes(formData.pais.toLowerCase()))
    .slice(0, 10); // Limitar a 10 resultados

  // Manejar envío del formulario
  const handleSubmit = async (e) => {
    e.preventDefault();

    if (onSubmit) {
      // Si se proporciona un callback personalizado
      onSubmit(formData);
    } else {
      // Envío tradicional mediante form action
      const form = e.target;
      const formDataToSend = new FormData();

      // Agregar todos los campos al FormData
      Object.keys(formData).forEach((key) => {
        if (key === 'redes') {
          Object.keys(formData.redes).forEach((redesKey) => {
            formDataToSend.append(
              `redes[${redesKey}]`,
              formData.redes[redesKey]
            );
          });
        } else {
          formDataToSend.append(key, formData[key]);
        }
      });

      // Agregar CSRF token si existe
      const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');
      if (csrfToken) {
        formDataToSend.append('_token', csrfToken);
      }

      try {
        const response = await fetch(form.action || '/convenios', {
          method: 'POST',
          body: formDataToSend,
        });

        if (response.ok) {
          // Redirigir o mostrar mensaje de éxito
          window.location.href = '/convenios';
        } else {
          console.error('Error en la respuesta:', response.statusText);
        }
      } catch (error) {
        console.error('Error al enviar el formulario:', error);
      }
    }
  };

  return (
    <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div className="p-6 bg-white border-b border-gray-200">
          <form onSubmit={handleSubmit} className="w-full max-w-screen mx-auto">
            {/* Tipo de Documento */}
            <div className="mb-4">
              <label
                htmlFor="tipo_documento"
                className="block text-gray-700 text-sm font-bold mb-2"
              >
                Tipo de Documento:
              </label>
              <select
                name="tipo_documento"
                id="tipo_documento"
                value={formData.tipo_documento}
                onChange={handleInputChange}
                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                required
              >
                {documentos.map((doc) => (
                  <option key={doc.cod_dian} value={doc.cod_dian}>
                    {doc.nombre}
                  </option>
                ))}
              </select>
            </div>

            {/* Número de Documento */}
            <div className="mb-4">
              <label
                htmlFor="numero_documento"
                className="block text-gray-700 text-sm font-bold mb-2"
              >
                Número de Documento:
              </label>
              <input
                type="text"
                name="numero_documento"
                id="numero_documento"
                value={formData.numero_documento}
                onChange={handleInputChange}
                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                required
              />
            </div>

            {/* Razón Social */}
            <div className="mb-4 max-w-screen-sm">
              <label
                htmlFor="razon_social"
                className="block text-gray-700 text-sm font-bold mb-2"
              >
                Razón Social:
              </label>
              <input
                type="text"
                name="razon_social"
                id="razon_social"
                value={formData.razon_social}
                onChange={handleInputChange}
                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                required
              />
            </div>

            <hr className="my-8 border-1 border-borders" />

            {/* Teléfono y Correo */}
            <div className="row-inputs pt-2 w-full md:flex justify-between gap-2">
              <div className="w-full pb-2">
                <label
                  htmlFor="telefono"
                  className="block text-gray-700 text-sm font-bold mb-2"
                >
                  Teléfono
                </label>
                <input
                  type="number"
                  id="telefono"
                  name="telefono"
                  value={formData.telefono}
                  onChange={handleInputChange}
                  className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                />
              </div>
              <div className="w-full pb-2">
                <label
                  htmlFor="correo"
                  className="block text-gray-700 text-sm font-bold mb-2"
                >
                  Correo
                </label>
                <input
                  type="email"
                  id="correo"
                  name="correo"
                  value={formData.correo}
                  onChange={handleInputChange}
                  placeholder="example@mail.com"
                  className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                />
              </div>
            </div>

            {/* Ciudad y Dirección */}
            <div className="row-inputs pt-2 w-full md:grid md:grid-cols-3 gap-2">
              <div className="w-full pb-2">
                <label
                  htmlFor="municipio"
                  className="block text-gray-700 text-sm font-bold mb-2"
                >
                  Ciudad
                </label>
                <input
                  type="text"
                  id="municipio"
                  value={formData.municipio}
                  readOnly
                  className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-gray-100"
                />
              </div>
              <div className="w-full pb-2 md:col-span-2">
                <label
                  htmlFor="direccion"
                  className="block text-gray-700 text-sm font-bold mb-2"
                >
                  Dirección
                </label>
                <input
                  type="text"
                  id="direccion"
                  name="direccion"
                  value={formData.direccion}
                  onChange={handleInputChange}
                  className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                />
              </div>
            </div>

            {/* País con Autocomplete Reactivo */}
            <div className="mb-4 max-w-screen-sm">
              <label
                htmlFor="pais"
                className="block text-gray-700 text-sm font-bold mb-2"
              >
                País:
              </label>
              <AutocompleteInput
                value={formData.pais}
                onChange={handlePaisChange}
                suggestions={paises}
                placeholder="Empieza a escribir (mínimo 3 letras)..."
                minLengthToShow={3}
                allowCustom={true}
              />
            </div>

            {/* Redes Sociales */}
            <div className="mb-4 max-w-screen-sm">
              <label
                htmlFor="whatsapp"
                className="block text-gray-700 text-sm font-bold mb-2"
              >
                WhatsApp:
              </label>
              <input
                type="text"
                id="whatsapp"
                name="redes[whatsapp]"
                value={formData.redes.whatsapp}
                onChange={handleRedesChange}
                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              />
            </div>

            <div className="mb-4 max-w-screen-sm">
              <label
                htmlFor="maps"
                className="block text-gray-700 text-sm font-bold mb-2"
              >
                Google Maps:
              </label>
              <input
                type="text"
                id="maps"
                name="redes[maps]"
                value={formData.redes.maps}
                onChange={handleRedesChange}
                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              />
            </div>

            <div className="mb-4 max-w-screen-sm">
              <label
                htmlFor="linkedin"
                className="block text-gray-700 text-sm font-bold mb-2"
              >
                LinkedIn:
              </label>
              <input
                type="text"
                id="linkedin"
                name="redes[linkedin]"
                value={formData.redes.linkedin}
                onChange={handleRedesChange}
                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              />
            </div>

            <div className="mb-4 max-w-screen-sm">
              <label
                htmlFor="facebook"
                className="block text-gray-700 text-sm font-bold mb-2"
              >
                Facebook:
              </label>
              <input
                type="text"
                id="facebook"
                name="redes[facebook]"
                value={formData.redes.facebook}
                onChange={handleRedesChange}
                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              />
            </div>

            <div className="mb-4 max-w-screen-sm">
              <label
                htmlFor="instagram"
                className="block text-gray-700 text-sm font-bold mb-2"
              >
                Instagram:
              </label>
              <input
                type="text"
                id="instagram"
                name="redes[instagram]"
                value={formData.redes.instagram}
                onChange={handleRedesChange}
                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              />
            </div>

            <div className="mb-4 max-w-screen-sm">
              <label
                htmlFor="tiktok"
                className="block text-gray-700 text-sm font-bold mb-2"
              >
                TikTok:
              </label>
              <input
                type="text"
                id="tiktok"
                name="redes[tiktok]"
                value={formData.redes.tiktok}
                onChange={handleRedesChange}
                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              />
            </div>

            <div className="mb-4 max-w-screen-sm">
              <label
                htmlFor="youtube"
                className="block text-gray-700 text-sm font-bold mb-2"
              >
                YouTube:
              </label>
              <input
                type="text"
                id="youtube"
                name="redes[youtube]"
                value={formData.redes.youtube}
                onChange={handleRedesChange}
                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              />
            </div>

            <div className="mb-4 max-w-screen-sm">
              <label
                htmlFor="website"
                className="block text-gray-700 text-sm font-bold mb-2"
              >
                Sitio Web:
              </label>
              <input
                type="text"
                id="website"
                name="redes[website]"
                value={formData.redes.website}
                onChange={handleRedesChange}
                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              />
            </div>

            <div className="mb-4 max-w-screen-sm">
              <label
                htmlFor="otras_redes"
                className="block text-gray-700 text-sm font-bold mb-2"
              >
                Otras Redes Sociales:
              </label>
              <input
                type="text"
                id="otras_redes"
                name="redes[otras_redes]"
                value={formData.redes.otras_redes}
                onChange={handleRedesChange}
                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              />
            </div>

            {/* Botón de Envío */}
            <div className="row-inputs py-8 w-full flex justify-center gap-2">
              <button
                type="submit"
                className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-40"
              >
                Guardar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}
