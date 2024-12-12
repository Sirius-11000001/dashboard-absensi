<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Introduce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/js/bootstrap.min.js">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/style/kelompok-1.css">
    <style>
      body, html {
        height: 100%;
        margin: 0;
        font-family: 'Roboto',sans-serif;
      }
      .hero {
        background: black;
        z-index: -2;
      }
      .bg-video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: -1;
      }
      .bg-video video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: blur(1px), grayscale(20%);
        opacity: 0.5;
        animation: fade 10s ease;
        z-index: 1;
      }
      @keyframes fade {
        0% {
          opacity: 0;
        }
        100% {
          opacity: 0.5;
        }
      }
      .hero {
        position: relative;
        height: 100vh;
        display: flex;
        justify-content: center;
        text-align: center;
        color: white;
      }
      .main-hero {
        margin-top: 20%;
      }
      .main-hero h1 {
        text-shadow: 5px 5px 5px rgba(255, 255, 255, 0.3);
        animation: slide 4s ease infinite;
      }
      @keyframes slide {
        0% {
          transform: translateY(-200px);
          opacity: 0;
        }
        100% {
          transform: translateY(0);
          opacity: 1;
        }
      }
      .detail-main-hero {
        overflow: hidden;
      }
      .detail-main-hero .p1 {
        padding: 10px;
        text-shadow: 10px 10px 10px rgba(255, 255, 255, 0.3);
        animation: meluncur 4s ease infinite;
      }
      @keyframes meluncur {
        0% {
          transform: translateX(-200px);
          opacity: 0;
        }
        100% {
          transform: translateX(0);
          opacity: 1;
        }
      }
      .detail-main-hero .p2 {
        padding: 10px;
        text-shadow: 10px 10px 10px rgba(255, 255, 255, 0.3);
        animation: ngesot 4s ease infinite;
      }
      @keyframes ngesot {
        0% {
          transform: translateX(200px);
          opacity: 0;
        }
        100% {
          transform: translateX(0);
          opacity: 1;
        }
      }
    </style>
  </head>
  <body>
    <section>
      <div id="hero" class="hero">
        <div class="bg-video">
          <video loop muted autoplay>
            <source src="http://localhost/dashboard-absensi/assets/media/coding-background.mp4" type="video/mp4">
            Your browser does not support the video tag.
          </video>
        </div>
        <div class="container">
          <div class="main-hero">
            <h1>Welcome to Naluri Ai</h1>
            <div class="detail-main-hero">
              <h5 class="p1"><strong>We are Kelompok 1</strong></h5>
              <h5 class="p2">Presenting Absensi QR Code Berbasis Web</h5>
            </div>
          </div>
        </div>
      </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="http://localhost/dashboard-absensi/auth/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  </body>
</html>
