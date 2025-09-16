import React from 'react';
import { useState } from 'react';



function TestComponent() {

const [count, setCount] = useState(0);

function increment() {
    setCount(count + 1);
}


    return React.createElement('div', null, [
        React.createElement('h1', null, 'React Test Component - Count: ' + count),
        React.createElement('button', { 
            className: 'px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors duration-300',
            onClick: () => increment()
        }, 'Click Me')
    ]);
}

export default TestComponent;