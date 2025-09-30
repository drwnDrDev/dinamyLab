// ExamenesSection.jsx
import { useExamenes } from './hooks/useExamenes';
import TablaExamenes from './TablaExamenes';
import ModalExamenes from './ModalExamenes';

const DatosExamenes = ({ examenes, onUpdate }) => {
  const disponibles = useExamenes(); // desde LocalStorage

  const [isModalOpen, setIsModalOpen] = useState(false);

  const handleAgregar = (nuevoExamen) => {
    const yaExiste = examenes.some(e => e.codigo === nuevoExamen.codigo);
    if (!yaExiste) {
      onUpdate([...examenes, { ...nuevoExamen, cantidad: 1 }]);
    }
  };

  return (
    <section className='examenes_container max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8 bg-background rounded-xl border border-secondary shadow-md mb-4'>
      <h2>Exámenes</h2>
      <TablaExamenes/>
      <button onClick={() => setIsModalOpen(true)}>Agregar examen</button>

      {isModalOpen && (
        <ModalExamenes
          disponibles={disponibles}
          examenesActuales={examenes}
          onAgregar={handleAgregar}
          onClose={() => setIsModalOpen(false)}
        />
      )}
    </section>
  );
};
export default DatosExamenes;