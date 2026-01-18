import React, { useState, useEffect, useCallback, memo } from 'react';

const Typewriter = memo(({ text, typingSpeed = 50 }) => {
  const [displayedText, setDisplayedText] = useState('');
  const [currentIndex, setCurrentIndex] = useState(0);

  useEffect(() => {
    if (currentIndex < text.length) {
      const timeout = setTimeout(() => {
        setDisplayedText((prevText) => prevText + text[currentIndex]);
        setCurrentIndex((prevIndex) => prevIndex + 1);
      }, typingSpeed);

      return () => clearTimeout(timeout);
    }
  }, [currentIndex, text, typingSpeed]);

  return <span className="inline-block">{displayedText}</span>;
});

Typewriter.displayName = 'Typewriter';

export default Typewriter;
