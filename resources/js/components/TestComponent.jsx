import React from 'react';
import { useState } from 'react';


function TestComponent(props) {

const [count, setCount] = useState(0);
const [par, setPar] = useState(props.authUser.nombre === "luis");
function increment() {
    setCount(count + 1);
    setPar(!par);
}



    return React.createElement('div', null, [
        React.createElement('h1', null, 'React Test Component - Count: ' + count),
        React.createElement('p', null, 'Login User: ' + props.loginUser),
        React.createElement('p', null, 'Register User: ' + props.registerUser),

        React.createElement('button', {
            className: par? 'px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors duration-300': 'px-4 py-2 bg-red-500 text-white rounded-md hover:bg-green-600 transition-colors duration-300',
            onClick: () => increment()
        }, 'Click Me')
    ]);
}



export default TestComponent;
