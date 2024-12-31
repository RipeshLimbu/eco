document.addEventListener("DOMContentLoaded", () => {
    const counters = document.querySelectorAll(".stat-number");
    const speed = 200; // Adjust to control speed
  
    counters.forEach(counter => {
      const updateCount = () => {
        const target = +counter.getAttribute("data-target");
        const count = +counter.innerText;
  
        // Calculate increment
        const increment = target / speed;
  
        if (count < target) {
          counter.innerText = Math.ceil(count + increment);
          setTimeout(updateCount, 20);
        } else {
          const symbol = counter.parentElement.innerText.includes("%") ? "%" : 
                         counter.parentElement.innerText.includes("+") ? "+" : 
                         counter.parentElement.innerText.includes("/7") ? "/7" : "";
          counter.innerText = target + symbol; // Append the symbol
        }
      };
  
      updateCount();
    });
  });
  