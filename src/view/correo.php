<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cambio de Contraseña - AriModas</title>
  <style>
    body, p, div, h1, h2, h3, a {
      margin: 0;
      padding: 0;
      font-family: Tahoma, Geneva, Verdana, sans-serif;
      color: #444444;
      line-height: 1.6;
    }
    body {
      background-color: #f4f6f9;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    }
    a {
      color: #2563eb;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
    .container {
      max-width: 600px;
      margin: 40px auto;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.07);
      border: 1px solid #e2e8f0;
      overflow: hidden;
    }
    .header {
      background: linear-gradient(90deg,rgb(189, 208, 238) 0%, #2563eb 100%);
      padding: 30px;
      text-align: center;
      color: white;
    }
    .header img {
      max-width: 100px;
      margin-bottom: 10px;
     
    }
    .header h1 {
      font-weight: 700;
      font-size: 24px;
      margin-top: 10px;
    }
    .body {
      padding: 35px 30px 40px;
      font-size: 16px;
      color: #555555;
    }
    .body h2 {
      font-weight: 600;
      font-size: 22px;
      margin-bottom: 18px;
      color: #222222;
    }
    .body p {
      margin-bottom: 18px;
    }
    .btn-primary {
      display: inline-block;
      background-color:rgb(152, 189, 250);
      color: #fff !important;
      padding: 14px 28px;
      font-weight: 600;
      border-radius: 8px;
      font-size: 16px;
      margin: 25px 0;
      text-align: center;
      box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
      transition: background-color 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #1d4ed8;
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
    @media screen and (max-width: 620px) {
      .container {
        width: 95% !important;
        margin: 15px auto;
        border-radius: 8px;
      }
      .body {
        padding: 25px 20px 30px;
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
  <div class="container" style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif;">
    <!-- Header -->
    <div class="header" style="text-align: center; padding: 20px 0;">
    <img src="./img/LogoAri.png" alt="" style="Width: 10px">
      <h1 style="margin: 0; color: #333;">AriModas</h1>
    </div>
    <!-- Body -->
    <div class="body" style="padding: 20px; background-color: #f9f9f9; border-radius: 8px;">
      <h2 style="color: #333;">Hola, <span id="nombreUsuario" style="color: #007bff;">usuario</span> 👋</h2>
      <p>Hemos recibido una solicitud para cambiar tu contraseña. Si fuiste tú quien hizo esta solicitud, puedes continuar el proceso haciendo clic en el botón a continuación.</p>
      <p>Por seguridad, este enlace solo estará disponible durante las próximas 24 horas. Si no realizaste esta solicitud, puedes ignorar este mensaje sin problemas.</p>

      <div style="text-align: center; margin: 30px 0;">
        <a href="{{enlace_cambio_contrasena}}" style="background-color: #007bff; color: white; padding: 12px 20px; border-radius: 6px; text-decoration: none; display: inline-block;">Cambiar contraseña</a>
      </div>

      <p>Gracias por confiar en nosotros.<br>— El equipo de <strong>AriModas</strong></p>
    </div>

    <!-- Footer -->
    <div class="footer" style="text-align: center; font-size: 13px; color: #888; margin-top: 20px;">
      <p>© 2025 AriModas. Todos los derechos reservados.</p>
      <p><a href="{{link_baja_suscripcion}}" style="color: #888; text-decoration: underline;">Cancelar suscripción</a></p>
    </div>
  </div>
</body>

</html>