<?php
session_start();
$tipe_form = $_SESSION['tipe_form'];

?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Terima Kasih - RS Ekahusada</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .thank-you-container {
      max-width: 600px;
      margin: 60px auto;
      text-align: center;
      background: white;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 102, 204, 0.15);
    }

    .thank-you-icon {
      width: 100px;
      height: 100px;
      background: #e6f7ff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 25px;
    }

    .thank-you-icon svg {
      width: 50px;
      height: 50px;
      fill: #10b981;
    }

    .thank-you-title {
      font-size: 32px;
      color: #2c3e50;
      margin-bottom: 15px;
      font-weight: 700;
    }

    .thank-you-message {
      font-size: 18px;
      color: #555;
      line-height: 1.6;
      margin-bottom: 30px;
    }

    .back-button {
      background: linear-gradient(to right, #0066cc, #00b3b3);
      color: white;
      border: none;
      padding: 14px 35px;
      font-size: 18px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s;
      text-decoration: none;
      display: inline-block;
    }

    .back-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(0, 102, 204, 0.4);
    }

    .footer-thankyou {
      margin-top: 40px;
      text-align: center;
      color: #7f8c8d;
      font-size: 14px;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="thank-you-container">
      <div class="thank-you-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
      </div>
      <h1 class="thank-you-title">Terima Kasih!</h1>
      <p class="thank-you-message">
        Terima kasih atas partisipasi Anda dalam mengisi kuesioner kepuasan pasien RS Ekahusada.
      </p>
      <p class="thank-you-message">
        Masukan Anda sangat berharga untuk meningkatkan kualitas layanan kami.
      </p>
      <a href="<?php echo $tipe_form === 'kepuasan' ? 'kepuasan.php' : 'keluhan.php'; ?>" class="back-button">Kembali</a>
    </div>

    <div class="footer-thankyou">
      <p>RS Ekahusada - Melayani Dengan Sepenuh Hati</p>
    </div>
  </div>
</body>

</html>

