import React from 'react';

const steps = [
  { id: 1, label: 'Paciente' },
  { id: 2, label: 'Exámenes' },
  { id: 3, label: 'Datos Orden' },
  { id: 4, label: 'Resumen' },
];

const CheckIcon = () => (
  <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={3}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
  </svg>
);

const StepperOrden = ({ currentStep }) => {
  return (
    <nav aria-label="Progreso de la orden" className="mb-6">
      <ol className="flex items-center">
        {steps.map((step, index) => (
          <React.Fragment key={step.id}>
            {/* Paso */}
            <li className="flex flex-col items-center">
              <div
                className={`w-9 h-9 rounded-full flex items-center justify-center text-sm font-semibold transition-colors
                  ${currentStep > step.id
                    ? 'bg-primary text-white'
                    : currentStep === step.id
                    ? 'bg-primary text-white ring-4 ring-secondary'
                    : 'bg-gray-200 text-gray-500'
                  }`}
              >
                {currentStep > step.id ? <CheckIcon /> : step.id}
              </div>
              <span
                className={`mt-1.5 text-xs font-medium whitespace-nowrap
                  ${currentStep >= step.id ? 'text-titles' : 'text-gray-400'}`}
              >
                {step.label}
              </span>
            </li>
            {/* Línea conectora */}
            {index < steps.length - 1 && (
              <div
                className={`flex-1 h-0.5 mx-2 mb-5 transition-colors
                  ${currentStep > step.id ? 'bg-primary' : 'bg-gray-200'}`}
              />
            )}
          </React.Fragment>
        ))}
      </ol>
    </nav>
  );
};

export default StepperOrden;
