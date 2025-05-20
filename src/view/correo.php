<!DOCTYPE html>
<html lang="es" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Correo Empresarial Moderno</title>
  <style>
    /* Reset básico */
    body, p, div, h1, h2, h3, a {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #444444;
      line-height: 1.5;
    }
    body {
      background-color: #f9fafc;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    }
    a {
      color: #3b82f6;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
    .container {
      max-width: 600px;
      margin: 30px auto;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
      overflow: hidden;
      border: 1px solid #e2e8f0;
    }
    .header {
      background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
      padding: 25px 30px;
      text-align: center;
      color: white;
    }
    .header img {
      max-width: 140px;
      margin-bottom: 10px;
      filter: brightness(0) invert(1);
    }
    .header h1 {
      font-weight: 700;
      font-size: 24px;
      letter-spacing: 1px;
    }
    .body {
      padding: 30px 30px 40px;
      font-size: 16px;
      color: #555555;
    }
    .body h2 {
      font-weight: 600;
      font-size: 22px;
      margin-bottom: 15px;
      color: #222222;
    }
    .body p {
      margin-bottom: 18px;
    }
    .btn-primary {
      display: inline-block;
      background-color: #3b82f6;
      color: #fff !important;
      padding: 14px 28px;
      font-weight: 600;
      border-radius: 8px;
      font-size: 16px;
      margin: 20px 0;
      text-align: center;
      box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
      transition: background-color 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #2563eb;
    }
    .footer {
      background-color: #f1f5f9;
      padding: 20px 30px;
      font-size: 13px;
      text-align: center;
      color: #888888;
      border-top: 1px solid #e2e8f0;
    }
    .footer a {
      color: #3b82f6;
    }
    /* Responsive */
    @media screen and (max-width: 620px) {
      .container {
        width: 95% !important;
        margin: 15px auto;
        border-radius: 8px;
      }
      .body {
        padding: 20px 20px 30px;
        font-size: 15px;
      }
      .btn-primary {
        width: 100%;
        box-sizing: border-box;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Header -->
    <div class="header">
      <img src="https://tuempresa.com/logo.png" alt="Logo Empresa" />
      <h1>Nombre de tu Empresa</h1>
    </div>

    <!-- Body -->
    <div class="body">
      <h2>Hola, {{nombre_cliente}} 👋</h2>
      <p>Gracias por ser parte de nuestra comunidad. Queremos mantenerte informado sobre las últimas novedades, promociones exclusivas y contenido de valor para ti.</p>
      <p>Explora nuestra web y descubre todo lo que tenemos preparado para ayudarte a crecer y mejorar cada día.</p>

      <a href="https://tuempresa.com" class="btn-primary" target="_blank" rel="noopener noreferrer">Visita nuestro sitio</a>

      <p>Si tienes preguntas, estamos aquí para ayudarte. No dudes en contactarnos.</p>

      <p>¡Un saludo cordial!<br />Equipo de <strong>Nombre de tu Empresa</strong></p>
    </div>

    <!-- Footer -->
    <div class="footer">
      <p>© 2025 Nombre de tu Empresa. Todos los derechos reservados.</p>
      <p><a href="{{link_baja_suscripcion}}">Cancelar suscripción</a></p>
    </div>
  </div>
</body>
</html>
