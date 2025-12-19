import React from 'react';

export const Loader = () => {
    return (
        <div className="fixed inset-0 flex justify-center items-center h-full z-50 bg-white bg-opacity-50">
            <div className="loader animate-spin rounded-full border-8 border-t-primary h-16 w-16"></div>
        </div>
    );
}

export default Loader;

