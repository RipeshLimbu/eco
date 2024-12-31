<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hero Section</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  <style>
        .navbar-custom {
      background-color: white;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      padding: 0.8rem 1.5rem; /* Top-bottom: 0.8rem, Left-right: 1.5rem */
    }
    .navbar-custom .nav-link {
      color: #4a5568; /* gray-600 */
      padding: 0.5rem 1rem; /* Top-bottom: 0.5rem, Left-right: 1rem */
    }
    .navbar-custom .nav-link:hover {
      color: #38a169; /* green-600 */
    }
    .navbar-brand-icon {
      color: #38a169; /* green-600 */
      font-size: 1.5rem;
    }
    .navbar-custom .btn-success {
      padding: 0.6rem 1.5rem; /* Top-bottom: 0.6rem, Left-right: 1.5rem */
    }
    .hero {
      position: relative;
      background-color: white;
      overflow: hidden;
      margin: 100px;
    }
    .hero img {
      height: 100%;
      object-fit: cover;
    }
    .hero-content {
      z-index: 10;
      background-color: white;
    }
    .btn-custom {
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .btn-custom .icon {
      margin-left: 8px;
    }
     /* Features Section Styles */
   .feature-card {
      background-color: white;
      padding: 1.25rem;
      border-radius: 0.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
    }

    .feature-icon {
      font-size: 2rem;
      color: #28a745;
      margin-bottom: 1rem;
    }

    .feature-title {
      font-size: 1rem;
      font-weight: 500;
      margin-bottom: 0.5rem;
    }

    .feature-description {
      color: #6c757d;
      font-size: 0.70rem;
    }
  </style>
</head>
<body>
      <!-- Top Navbar -->
      <nav class="navbar navbar-expand-lg bg-success navbar-dark py-2">
        <div class="container">
          <div class="d-flex justify-content-end w-100">
            <a href="schedule.html" class="d-flex align-items-center text-white text-decoration-none me-3 hover-effect">
              <i class="bi bi-calendar3 me-1"></i>
              <span>Schedule</span>
            </a>
            <a href="../login.php" class="d-flex align-items-center text-white text-decoration-none me-3 hover-effect">
            <i class="bi bi-person me-1"></i>
            <span>Log in</span>
            </a>
            <a href="../register.php" class="d-flex align-items-center text-white text-decoration-none hover-effect">
              <i class="bi bi-person me-1"></i>
              <span>Sign up</span>
            </a>
          </div>
        </div>
    </nav>
  <nav class="navbar navbar-light navbar-custom">
    <div class="container d-flex justify-content-between align-items-center">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <span class="navbar-brand-icon">&#x267B;</span>
        <span class="ms-2 fs-4 fw-bold text-success">ecoManage</span>      </a>
      <div class="d-flex align-items-center">
        <a class="nav-link" href="Residential.html">Residential</a>
        <a class="nav-link" href="Commercial.html">Commercial</a>
        <a class="nav-link" href="About Us.html">About Us</a>
        <a class="nav-link" href="Services.html">Services</a>
        <a href="contact.html" class="btn btn-success text-white ms-3 px-4 py-2">Contact Us</a>
      </div>
    </div>
  </nav>
  <div class="hero">
    <div class="container py-5">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <div class="hero-content">
            <h1 class="display-4 fw-bold">
              <span class="d-block">Sustainable Waste</span>
              <span class="d-block text-success">Management Solutions</span>
            </h1>
            <p class="mt-4 text-muted">
              Leading the way in environmentally responsible waste management. We're committed to creating a cleaner, greener future through innovative recycling and waste reduction solutions.
            </p>
            <div class="mt-4 d-flex">
              <a href="../register.php" class="btn btn-success btn-lg me-3 btn-custom">
                Get Started
                <span class="icon">&rarr;</span>
              </a>
              <a href="#services" class="btn btn-outline-success btn-lg btn-custom">
                Learn More
                <span class="icon">&#x1f343;</span>
              </a>
            </div>
          </div>
        </div>
        <div class="col-lg-6 d-none d-lg-block">
          <img src="https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Waste management facility" class="img-fluid">
        </div>
      </div>
    </div>
  </div>
  <section class="py-5 bg-light">
    <div class="container">
      <div class="row g-3">
        <!-- Feature 1 -->
        <div class="col-6 col-md-4 col-lg-3">
          <div class="feature-card">
            <i class="bi bi-leaf feature-icon"></i>
            <h3 class="feature-title">Eco-Friendly Solutions</h3>
            <p class="feature-description">
              Committed to sustainable practices for future generations.
            </p>
          </div>
        </div>
        <!-- Feature 2 -->
        <div class="col-6 col-md-4 col-lg-3">
          <div class="feature-card">
            <i class="bi bi-buildings feature-icon"></i>
            <h3 class="feature-title">Commercial Services</h3>
            <p class="feature-description">
              Tailored waste management solutions for businesses of all sizes.
            </p>
          </div>
        </div>
        <!-- Feature 3 -->
        <div class="col-6 col-md-4 col-lg-3">
          <div class="feature-card">
            <i class="bi bi-recycle feature-icon"></i>
            <h3 class="feature-title">Recycling Programs</h3>
            <p class="feature-description">
              Comprehensive recycling services to minimize environmental impact.
            </p>
          </div>
        </div>
        <!-- Feature 4 -->
        <div class="col-6 col-md-4 col-lg-3">
          <div class="feature-card">
            <i class="bi bi-trophy feature-icon"></i>
            <h3 class="feature-title">Award-Winning Service</h3>
            <p class="feature-description">
              Recognized for excellence in customer service and environmental stewardship.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script> -->
</body>
</html>