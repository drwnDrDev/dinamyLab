import React from "react";

const CompletedCheck = () => {
  return (
    <div className="fixed inset-0 flex flex-col justify-center items-center z-50 bg-white bg-opacity-95">
      <div className="text-center flex flex-col items-center gap-4">
        <div className="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            className="h-10 w-10 text-green-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            strokeWidth={2.5}
          >
            <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <div>
          <p className="text-xl font-semibold text-titles">¡Orden creada!</p>
          <p className="mt-1 text-sm text-gray-500">Redirigiendo a la orden...</p>
        </div>
      </div>
    </div>
  );
};

export default CompletedCheck;
