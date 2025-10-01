// ExamenesSection.jsx
import { useExamenes } from './hooks/useExamenes';
import TablaExamenes from './TablaExamenes';
import ModalExamenes from './ModalExamenes';

const DatosExamenes = ({ formExamenes, onUpdate }) => {
  const { examenes, loading } = useExamenes(); // desde LocalStorage
  const disponibles = examenes || [];
  const [isModalOpen, setIsModalOpen] = useState(false);

  const handleAgregar = (nuevoExamen) => {
    const yaExiste = formExamenes.some(e => e.codigo === nuevoExamen.codigo);
    if (!yaExiste) {
      onUpdate([...formExamenes, { ...nuevoExamen, cantidad: 1 }]);
    }
  };
  const handleRemove = (index) => {
    const nuevosExamenes = [...formExamenes];
    nuevosExamenes.splice(index, 1);
    onUpdate(nuevosExamenes);
  }

  console.log('Exámenes disponibles, desde localStorage:', disponibles);
  return (
    <section className='examenes_container max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8 bg-background rounded-xl border border-secondary shadow-md mb-4'>
      <h2>Exámenes</h2>
      <button onClick={() => setIsModalOpen(true)}>Agregar examen</button>
      {formExamenes.length === 0 && <p>No hay exámenes seleccionados.</p>}

      {formExamenes.length > 0 && (
        <TablaExamenes
          examenes={formExamenes}
          onRemove={handleRemove}
          onCantidadChange={(idx, nuevaCantidad) => {
            const nuevosExamenes = [...formExamenes];
            nuevosExamenes[idx].cantidad = nuevaCantidad;
            onUpdate(nuevosExamenes);
          }}
        />
      )}

      {isModalOpen && (
        <ModalExamenes
          disponibles={disponibles}
          examenesActuales={formExamenes}
          onAgregar={handleAgregar}
          onClose={() => setIsModalOpen(false)}
        />
      )}
    </section>
  );
};
export default DatosExamenes;