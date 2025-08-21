<x-app-layout>

    
    @if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded-md mb-4">
        {{ session('success') }}
    </div>
    @endif
    <x-canva>
        <div class="py-4 flex justify-between items-center">
            <div>
                <p class="text-2xl font-bold leading-tight tracking-[-0.015em]">{{ $persona->nombreCompleto() }}</p>
                <p class="text-titles">Paciente ID: {{ $persona->numero_documento }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('personas.edit', $persona) }}">
                    <x-primary-button>Editar</x-primary-button>
                </a>
                <a href="{{route('facturas.create',$persona)}}">
                    <x-secondary-button>Facturar</x-secondary-button>
                </a>
                <a href="{{route('resultados.historia',$persona)}}">
                    <x-secondary-button>Resultados</x-secondary-button>
                </a>
            </div>

        </div>
       
        <div class="flex border-b border-borders">
            <h2 class="text-2xl font-bold leading-tight tracking-[-0.015em] p-4 border-b-4 border-primary">Información del Paciente</h2>
        </div>
        
        <div class="py-4 grid grid-cols-2" id="info">
            <!-- Fecha de Nacimiento -->
            <div class="flex flex-col gap-1 border-b border-borders py-4 pr-2">
                <p class="text-titles font-normal leading-normal">Fecha de Nacimiento</p>
                <div class="flex justify-between items-center">
                    <p class="font-normal leading-normal" id="fecha_text">{{ $persona->fecha_nacimiento->format('d/m/Y') }}</p>
                    <input type="date" id="fecha_input" class="hidden w-2/3 rounded-md border-gray-300" 
                        value="{{ $persona->fecha_nacimiento->format('Y-m-d') }}">
                    <button data-field="fecha" data-persona="{{ $persona->id }}" 
                        class="edit-btn px-2 py-1 text-sm bg-primary text-white rounded hover:bg-primary-dark">
                        Editar
                    </button>
                </div>
            </div>

            <!-- Sexo -->
            <div class="flex flex-col gap-1 border-b border-borders py-4 pl-2">
                <p class="text-titles font-normal leading-normal">Sexo</p>
                <div class="flex justify-between items-center">
                    <p class="font-normal leading-normal" id="sexo_text">{{$persona->sexo==='M' ? 'Masculino':'Femenino'}}</p>
                    <select id="sexo_input" class="hidden w-2/3 rounded-md border-gray-300">
                        <option value="M" {{$persona->sexo==='M' ? 'selected' : ''}}>Masculino</option>
                        <option value="F" {{$persona->sexo==='F' ? 'selected' : ''}}>Femenino</option>
                    </select>
                    <button data-field="sexo" data-persona="{{ $persona->id }}" 
                        class="edit-btn px-2 py-1 text-sm bg-primary text-white rounded hover:bg-primary-dark">
                        Editar
                    </button>
                </div>
            </div>
            <div class="flex flex-col gap-1 border-b border-borders py-4 pr-2">
                <p class="text-titles  font-normal leading-normal">Telefonos</p>

                <p class=" font-normal leading-normal">
                    @if($persona->telefonos->isEmpty())
                    No tiene telefonos registrados
                    @else
                    @foreach($persona->telefonos as $telefono)
                    <span>{{ $telefono->numero}} </span>
                    @endforeach
                    @endif

                </p>
            </div>

            <!-- se debe procurar mostrar todos los elementos asi no existan datos -->

            @if ( $persona->direccion)

            <div class="flex flex-col gap-1 border-b border-borders py-4 pl-2">
                <p class="text-titles  font-normal leading-normal">Muncipio</p>
                <p class=" font-normal leading-normal">{{ $persona->direccion->municipio->municipio }}-{{ $persona->direccion->municipio->departamento }}</p>
            </div>

            <div class="flex flex-col gap-1 border-b border-borders py-4 pr-2">
                <p class="text-titles  font-normal leading-normal">Dirección</p>
                <p class=" font-normal leading-normal">{{ $persona->direccion->direccion}}</p>
            </div>
            @endif

            <div class="flex flex-col gap-1 border-b border-borders py-4 pr-2">
                <p class="text-titles  font-normal leading-normal">EPS</p>
                <p class=" font-normal leading-normal">{{ optional($persona->afiliacionSalud)->eps?? 'Sin Información'}}</p>
            </div>
        </div>

        <div class="flex border-b border-borders">
        <h2 class="text-2xl font-bold leading-tight tracking-[-0.015em] p-4 border-b-4 border-primary">Historia Clínica</h2>
        </div>
        <div class="py-4" id="historia">
            <div class="flex overflow-hidden rounded-xl border border-borders">
                @isset($procedimientos['terminado'])
                <table class="flex-1">
                    <thead>
                        <tr class="">
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">{{__('Date')}}</th>
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">{{__('Test')}}</th>
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">{{__('Status')}}</th>
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">{{__('Result')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($procedimientos['terminado'] as $procedimiento)

                        <tr class="border-t border-borders hover:bg-secondary">
                            <td class="px-3 py-4"><span class="w-40">{{ $procedimiento->fecha}}</span></td>
                            <td class="px-3 py-4"><span class="w-40">{{ $procedimiento->orden_id }}</span></td>
                            <td class="px-3 py-4"><span class="w-60">{{ $procedimiento->examen->nombre }}</span></td>
                            <td class="px-3 py-4">
                                <a href="{{ route('resultados.show', $procedimiento) }}">
                                    <span class="w-40 text-titles">{{ $procedimiento->estado }}</span>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                        
                <p class="self-center p-4">No existen registros aun</p>
                        
                @endisset
            </div>
        </div>
        
        <section class="otra_info  mt-6">

        <div class="flex border-b border-borders">    
        <h2 class="text-2xl font-bold leading-tight tracking-[-0.015em] p-4 border-b-4 border-primary">Examenes en Proceso</h2>
        </div>
            <div class="py-4" id="historia">
                <div class="flex overflow-hidden rounded-xl border border-borders">
                    @if(isset($procedimientos['en proceso']) && $procedimientos['en proceso']->count() > 0)
                    <table class="flex-1">
                        <thead>
                            <tr class="">
                                <th class="p-2 border border-spacing-1 border-stone-900 text-text w-40 text-sm font-medium leading-normal">{{__('Date')}}</th>
                                <th class="p-2 border border-spacing-1 border-stone-900 text-text w-40 text-sm font-medium leading-normal">{{__('Order')}}</th>
                                <th class="p-2 border border-spacing-1 border-stone-900 text-text w-40 text-sm font-medium leading-normal">{{__('Procedure')}}</th>
                                <th class="p-2 border border-spacing-1 border-stone-900 text-text w-40 text-sm font-medium leading-normal">{{__('Status')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($procedimientos['en proceso'] as $procedimiento)

                            <tr class="border-t border-borders hover:bg-secondary">
                                <td colspan="4" class="p-0">
                                    <a href="{{ route('resultados.create', $procedimiento) }}" class="flex w-full h-full cursor-pointer px-4 py-2 text-inherit no-underline">
                                        <span class="w-40">{{ $procedimiento->created_at->format('Y-m-d') }}</span>
                                        <span class="w-40 text-center">{{ $procedimiento->orden_id }}</span>
                                        <span class="w-60 text-center">{{ $procedimiento->examen->nombre }}</span>
                                        <span class="w-40 text-end">{{ $procedimiento->estado }}</span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="block text-center p-4">No existen registros aun</p>
                    @endif
                </div>
            </div>
        </section>
    </x-canva>
</x-app-layout>

@push('scripts')
<script>
    // Objeto para mantener el estado de edición
    const editState = {};

    // Función para inicializar los eventos
    function initializeEditors() {
        console.log('Inicializando editores...'); // Debug
        const editButtons = document.querySelectorAll('.edit-btn');
        console.log('Botones encontrados:', editButtons.length); // Debug
        
        editButtons.forEach(button => {
            button.addEventListener('click', handleEdit);
        });
    }

    // Función manejadora del evento click
    function handleEdit(event) {
        event.preventDefault();
        const field = this.getAttribute('data-field');
        const personaId = this.getAttribute('data-persona');
        console.log('Click en botón:', field, personaId); // Debug
        toggleEdit(field, personaId, event);
    }

    function toggleEdit(field, personaId, event) {
        console.log('Toggle edit:', field, personaId); // Debug
        const textElement = document.getElementById(`${field}_text`);
        const inputElement = document.getElementById(`${field}_input`);
        const button = event.currentTarget;

        if (!editState[field]) {
            // Modo edición
            editState[field] = true;
            textElement.classList.add('hidden');
            inputElement.classList.remove('hidden');
            button.textContent = 'Guardar';

            // Crear botón cancelar
            const cancelButton = document.createElement('button');
            cancelButton.textContent = 'Cancelar';
            cancelButton.className = 'ml-2 px-2 py-1 text-sm bg-gray-500 text-white rounded hover:bg-gray-600';
            cancelButton.addEventListener('click', (e) => cancelEdit(field, personaId, e));
            button.parentNode.insertBefore(cancelButton, button.nextSibling);

            // Actualizar el evento del botón editar
            button.removeEventListener('click', handleEdit);
            button.addEventListener('click', (e) => saveField(field, personaId, e));
        }
    }

    function cancelEdit(field, personaId, event) {
        event.preventDefault();
        const textElement = document.getElementById(`${field}_text`);
        const inputElement = document.getElementById(`${field}_input`);
        const button = event.currentTarget;
        const editButton = button.previousSibling;

        // Restaurar valor original
        inputElement.value = inputElement.defaultValue;
        
        // Volver a modo visualización
        textElement.classList.remove('hidden');
        inputElement.classList.add('hidden');
        editButton.textContent = 'Editar';
        editButton.onclick = (e) => toggleEdit(field, personaId, e);
        
        // Eliminar botón cancelar
        button.remove();
        editState[field] = false;
    }

    function saveField(field, personaId, event) {
        event.preventDefault();
        const inputElement = document.getElementById(`${field}_input`);
        const textElement = document.getElementById(`${field}_text`);
        const button = event.currentTarget;
        
        // Validar valor antes de enviar
        if (!inputElement.value.trim()) {
            showMessage('El campo no puede estar vacío', 'error');
            return;
        }

        // Deshabilitar botón mientras se procesa
        button.disabled = true;
        button.textContent = 'Guardando...';
        
        const formData = new FormData();
        formData.append(field, inputElement.value);
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');

        fetch(`/personas/${personaId}/update-field`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                textElement.textContent = data.formatted_value;
                textElement.classList.remove('hidden');
                inputElement.classList.add('hidden');
                
                // Actualizar valor por defecto
                inputElement.defaultValue = inputElement.value;
                
                // Restaurar botón
                button.textContent = 'Editar';
                button.disabled = false;
                button.onclick = (e) => toggleEdit(field, personaId, e);
                
                // Eliminar botón cancelar si existe
                const cancelButton = button.nextSibling;
                if (cancelButton) cancelButton.remove();
                
                editState[field] = false;
                showMessage('Campo actualizado correctamente', 'success');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            button.disabled = false;
            button.textContent = 'Guardar';
            showMessage('Error al actualizar el campo', 'error');
        });
    }

    function showMessage(message, type) {
        // Eliminar mensajes anteriores
        const existingMessages = document.querySelectorAll('.alert-message');
        existingMessages.forEach(msg => msg.remove());

        const div = document.createElement('div');
        div.textContent = message;
        div.className = `alert-message fixed top-4 right-4 p-4 rounded-md z-50 ${
            type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
        }`;
        document.body.appendChild(div);
        
        // Animación de fade out
        setTimeout(() => {
            div.style.transition = 'opacity 0.5s ease-out';
            div.style.opacity = '0';
            setTimeout(() => div.remove(), 500);
        }, 2500);
    }

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeEditors);
    } else {
        initializeEditors();
    }
</script>
@endpush