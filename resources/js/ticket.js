// window.print();

const totalOrden = parseFloat(document.getElementById('totalOrden').value) || 0;
const mediosPago = document.querySelectorAll('input[name^="medio_pago["]');
const formMediosPago = document.getElementById('formMediosPago');

const editarMediosPago = document.createElement('button');
editarMediosPago.id = 'agregarEditarButton';
editarMediosPago.textContent = 'Editar Medios de Pago';
editarMediosPago.className = 'bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600';
editarMediosPago.type = 'button';

editarMediosPago.addEventListener('click', () => {
    mediosPago.forEach(medio => {
        medio.disabled = false;
        medio.classList.remove('bg-gray-200', 'cursor-not-allowed');
        medio.classList.add('border-red-500', 'border-gray-300');
    });
    const diferenciaSpan = document.getElementById('diferencia');
    diferenciaSpan.textContent = 'Diferencia: ' + totalOrden;
});

const desabiltarMediosPago = () => {
    mediosPago.forEach(medio => {
        medio.disabled = true;
        medio.classList.add('bg-gray-200', 'cursor-not-allowed');
        medio.classList.remove('border-red-500', 'border-gray-300');
    });
    formMediosPago.appendChild(editarMediosPago);
};
const calcularTotalMediosPago = () => {
    return Array.from(mediosPago).reduce((total, medio) => {
        const valor = parseFloat(medio.value) || 0;
        return total + valor;
    }, 0);
};


formMediosPago.addEventListener('submit', (e) => {
    const totalMediosPago = calcularTotalMediosPago();
    if (totalMediosPago !== totalOrden) {
        e.preventDefault();
        mediosPago.forEach(medio => {
            if (medio.value === '' || parseFloat(medio.value) === 0) {
                medio.classList.add('border-red-500');
                medio.classList.remove('border-gray-300');
            } else {
                medio.classList.remove('border-red-500');
                medio.classList.add('border-gray-300');
            }
        });
        alert('La suma de los medios de pago debe ser igual al total de la orden.');
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const printButton = document.getElementById('printButton');
    if (printButton) {
        printButton.addEventListener('click', function () {
            window.print();
        });
    }
});


mediosPago.forEach(medio => {
    medio.addEventListener('blur', () => {
        const totalMediosPago = calcularTotalMediosPago();
        const diferencia = totalOrden - totalMediosPago;
     if(diferencia === 0) {
         desabiltarMediosPago();
        } else {
            medio.classList.remove('border-red-500');
            medio.classList.add('border-gray-300');
        }
        const diferenciaSpan = document.getElementById('diferencia');
        console.log('Total de la orden:', totalMediosPago);
        diferenciaSpan.textContent = 'Diferencia: ' + diferencia;
    });
});
