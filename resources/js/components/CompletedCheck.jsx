import React from "react";

const CompletedCheck = () => {

    return (
        <div className="fixed inset-0 flex justify-center items-center h-full z-50 bg-white bg-opacity-90">
            <div className="text-center">
                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <p className="mt-2 text-lg font-medium text-gray-800">Completado</p>
            </div>
        </div>
    );
}
export default CompletedCheck;
