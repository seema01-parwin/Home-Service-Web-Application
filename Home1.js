// Open Login Modal
document.getElementById("loginBtn").onclick = function() {
    document.getElementById("loginModal").style.display = "block";
  }
  document.getElementById("ltn").onclick = function() {
    document.getElementById("loginModal").style.display = "block";
  }
  
  // Open Register Modal
  document.getElementById("registerBtn").onclick = function() {
    document.getElementById("registerModal").style.display = "block";
  }
  document.getElementById("reg").onclick = function() {
    document.getElementById("registerModal").style.display = "block";
  }
  
  // Close Modal
  function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
  }
  
  // Close Modal if click outside
  window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
      event.target.style.display = "none";
    }
  }
  
  // Redirect Functions
  function redirectToLogin(type) {
    if (type === 'customer') {
      window.location.href = "CustomerLogin1.php";
    } else if (type === 'worker') {
      window.location.href = "WorkerLogin1.php";
    }
  }
  
  function redirectToRegister(type) {
    if (type === 'customer') {
      window.location.href = "CustomerRegister1.php";
    } else if (type === 'worker') {
      window.location.href = "WorkerRegister1.php";
    }
  }
  
  // Show loader
  function showLoader() {
    document.getElementById('loader').style.display = 'flex';
  }
  
  // Hide loader
  function hideLoader() {
    document.getElementById('loader').style.display = 'none';
  }
  
  // Redirect Book Now button
  function redirectToServices() {
    showLoader();
    setTimeout(() => {
      window.location.href = "Services1.php";
    }, 1000);
  }
  
  // Handle Search
  document.querySelector('.search-bar button').addEventListener('click', function() {
    let query = document.querySelector('.search-bar input').value.trim();
    if (query.length > 0) {
      showLoader();
      setTimeout(() => {
        window.location.href = "search1.php?query=" + encodeURIComponent(query);
      }, 1000);
    } else {
      alert('Please enter a search term.');
    }
  });
  
  setTimeout(() => {
    window.location.href = "search1.php?query=" + encodeURIComponent(query);
  }, 500);
  
  // Scroll to Top function
  function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
  
  // Show/hide back to top button on scroll
  window.addEventListener('scroll', function () {
    const btn = document.getElementById('backToTopBtn');
    if (window.scrollY > 300) {
      btn.style.display = 'block';
    } else {
      btn.style.display = 'none';
    }
  });
  
  // Scroll progress bar logic
  window.onscroll = function() {
    updateProgressBar();
    toggleBackToTopButton();
  };
  
  function updateProgressBar() {
    const scrollProgress = document.getElementById("scrollProgressBar");
    const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = (scrollTop / scrollHeight) * 100;
    scrollProgress.style.width = scrolled + "%";
  }
  
  // Moved from earlier for back-to-top button
  function toggleBackToTopButton() {
    const btn = document.getElementById('backToTopBtn');
    if (window.scrollY > 300) {
      btn.style.display = 'block';
    } else {
      btn.style.display = 'none';
    }
  }
  
  // Reveal elements on scroll
  function revealOnScroll() {
    const reveals = document.querySelectorAll('.reveal');
    for (let i = 0; i < reveals.length; i++) {
      const windowHeight = window.innerHeight;
      const revealTop = reveals[i].getBoundingClientRect().top;
      const revealPoint = 100;
  
      if (revealTop < windowHeight - revealPoint) {
        reveals[i].classList.add('active');
      } else {
        reveals[i].classList.remove('active');
      }
    }
  }
  
  window.addEventListener('scroll', () => {
    updateProgressBar();
    toggleBackToTopButton();
    revealOnScroll(); // <-- now calling reveal
  });
  