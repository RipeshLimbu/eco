function animatePercentage(id, start, end, duration) {
    const element = document.getElementById(id);
    let current = start;
    const increment = end > start ? 1 : -1;
    const stepTime = Math.abs(Math.floor(duration / (end - start)));
  
    const timer = setInterval(() => {
      current += increment;
      element.textContent = current;
      if (current === end) {
        clearInterval(timer);
      }
    }, stepTime);
  }
  
  window.onload = () => {
    animatePercentage("percent-1", 0, 75, 2000);
    animatePercentage("percent-2", 0, 80, 2000);
  };

  // Service Section
  const serviceCards = document.querySelectorAll('.service-card');

// Loop through each card and add mouseenter and mouseleave events
serviceCards.forEach((card) => {
  card.addEventListener('mouseenter', () => {
    // Change the background color and text color on hover
    card.style.backgroundColor = '#02a64b'; // Example: green background
    card.style.color = '#fff'; // White text
  });

  card.addEventListener('mouseleave', () => {
    // Reset to original colors
    if (card.classList.contains('green')) {
      card.style.backgroundColor = '#4caf50'; // Green for specific cards
      card.style.color = '#fff';
    } else {
      card.style.backgroundColor = '#fff'; // Default white background
      card.style.color = '#333'; // Default text color
    }
  });
});

// client satsfaction
document.querySelector('.client-satisfaction-box').addEventListener('mouseover', () => {
  alert('More than 3,200 satisfied clients!');
});





// commercial Section
