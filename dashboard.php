<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - RS Ekahusada</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #0078d7;
            --primary-dark: #005fa3;
            --secondary: #00c6d7;
            --accent: #00a896;
            --success: #00a896;
            --warning: #f59e0b;
            --danger: #ef4444;
            --light: #f9fafb;
            --dark: #1f2937;
            --gray: #6b7280;
            --text: #374151;
            --bg-gradient: linear-gradient(135deg, #0078d7 0%, #00a896 100%);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: linear-gradient(135deg, #0078d7 0%, #00a896 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            overflow-x: hidden;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 10% 20%, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 30%),
                radial-gradient(circle at 90% 80%, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 30%);
            z-index: -1;
        }

        .particle {
            position: fixed;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            z-index: -1;
            animation: float 15s infinite ease-in-out;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) translateX(0);
                opacity: 0.1;
            }

            50% {
                transform: translateY(-20px) translateX(10px);
                opacity: 0.3;
            }
        }

        .dashboard-container {
            max-width: 900px;
            width: 100%;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            position: relative;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-header {
            background: var(--bg-gradient);
            padding: 60px 40px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: pulse 8s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.1;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.2;
            }
        }

        .logo-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 25px;
            margin-bottom: 30px;
        }

        .logo-container {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .dashboard-title {
            color: white;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 12px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 0.6s ease-out;
        }

        .dashboard-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.2rem;
            font-weight: 500;
            animation: fadeInUp 0.6s ease-out 0.1s both;
        }

        .welcome-message {
            background: rgba(255, 255, 255, 0.15);
            padding: 15px;
            border-radius: 12px;
            margin-top: 20px;
            font-size: 1.05rem;
            color: white;
            backdrop-filter: blur(10px);
        }

        .dashboard-content {
            padding: 60px 40px;
            text-align: center;
        }

        .content-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease;
        }

        .content-description {
            color: var(--gray);
            font-size: 1.1rem;
            margin-bottom: 40px;
            line-height: 1.6;
            animation: fadeIn 0.5s ease 0.1s both;
        }

        .options-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .option-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            cursor: pointer;
            border: 2px solid #e5e7eb;
            position: relative;
            overflow: hidden;
        }

        .option-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            opacity: 0;
            transition: var(--transition);
            z-index: -1;
        }

        .option-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 120, 215, 0.3);
            border-color: var(--primary);
        }

        .option-card:hover::before {
            opacity: 0.08;
        }

        .option-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            color: var(--primary);
            transition: var(--transition);
        }

        .option-card:hover .option-icon {
            transform: scale(1.1) rotate(5deg);
            color: var(--secondary);
        }

        .option-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 15px;
            transition: var(--transition);
        }

        .option-card:hover .option-title {
            color: var(--primary);
        }

        .option-description {
            color: var(--gray);
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 50px;
            transition: var(--transition);
        }

        .option-card:hover .option-description {
            color: var(--dark);
        }

        .btn-option {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 35px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 6px 15px rgba(0, 120, 215, 0.3);
            text-decoration: none;
            width: 100%;
        }

        .btn-option:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 120, 215, 0.4);
        }

        .btn-option:active {
            transform: translateY(0);
        }

        .btn-option i {
            font-size: 1.3rem;
        }

        .btn-survey {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
        }

        .btn-complaint {
            background: linear-gradient(135deg, var(--accent), #008a7d);
        }

        .info-section {
            background: #f8f9fc;
            border-radius: 16px;
            padding: 30px;
            margin-top: 20px;
            border-left: 4px solid var(--primary);
        }

        .info-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-content {
            color: var(--dark);
            line-height: 1.7;
        }

        .info-content ul {
            text-align: left;
            margin-left: 20px;
            margin-top: 10px;
        }

        .info-content li {
            margin-bottom: 8px;
            color: var(--gray);
        }

        /* Footer */
        .dashboard-footer {
            background: var(--dark);
            color: white;
            padding: 40px 30px;
            text-align: center;
            margin-top: 30px;
        }

        .footer-content {
            max-width: 700px;
            margin: 0 auto;
        }

        .footer-title {
            font-size: 2rem;
            margin: 15px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            color: #00c6d7;
        }

        .footer-subtitle {
            color: #d1d5db;
            margin: 10px 0;
            font-size: 1.1rem;
        }

        .footer-contact {
            margin: 30px 0;
        }

        .contact-title {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: #00c6d7;
        }

        .contact-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }

        .contact-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .contact-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .contact-card h4 {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 1rem;
            margin-bottom: 10px;
            color: #00c6d7;
        }

        .contact-card p {
            color: #9ca3af;
            font-size: 1rem;
            line-height: 1.5;
        }

        .copyright {
            margin-top: 25px;
            color: #6b7280;
            font-size: 0.95rem;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-container {
                border-radius: 16px;
            }

            .dashboard-header {
                padding: 40px 20px 30px;
            }

            .logo-container {
                width: 100px;
                height: 100px;
            }

            .logo-icon {
                width: 60px;
                height: 60px;
            }

            .dashboard-title {
                font-size: 2rem;
            }

            .dashboard-subtitle {
                font-size: 1rem;
            }

            .dashboard-content {
                padding: 40px 20px;
            }

            .content-title {
                font-size: 1.5rem;
            }

            .options-container {
                grid-template-columns: 1fr;
            }

            .option-card {
                padding: 30px 20px;
            }

            .option-icon {
                font-size: 3.5rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .dashboard-container {
                margin: 10px;
            }

            .dashboard-header {
                padding: 30px 15px 25px;
            }

            .logo-container {
                width: 80px;
                height: 80px;
            }

            .logo-icon {
                width: 50px;
                height: 50px;
            }

            .dashboard-title {
                font-size: 1.8rem;
            }

            .dashboard-content {
                padding: 30px 15px;
            }

            .content-title {
                font-size: 1.4rem;
            }

            .option-card {
                padding: 25px 15px;
            }

            .option-icon {
                font-size: 3rem;
            }

            .option-title {
                font-size: 1.3rem;
            }

            .btn-option {
                padding: 14px 25px;
                font-size: 1rem;
            }
        }

        .gradient-border {
            position: relative;
            border-radius: 16px;
        }

        .gradient-border::before {
            content: '';
            position: absolute;
            inset: -2px;
            background: linear-gradient(45deg, var(--primary), var(--secondary), var(--accent), var(--success));
            border-radius: 18px;
            z-index: -1;
            animation: gradientRotate 8s linear infinite;
        }

        @keyframes gradientRotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const body = document.body;
            for (let i = 0; i < 20; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.width = `${Math.random() * 100 + 50}px`;
                particle.style.height = particle.style.width;
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;
                particle.style.animationDelay = `${Math.random() * 10}s`;
                body.appendChild(particle);
            }
        });
    </script>

    <div class="dashboard-container gradient-border">
        <div class="dashboard-header">
            <div class="logo-section">
                <div class="logo-container">
                    <img src="images/logo.png" alt="Logo RS Ekahusada" class="logo-icon">
                </div>
                <div>
                    <h1 class="dashboard-title">RS EKAHUSADA</h1>
                    <p class="dashboard-subtitle">Sistem Informasi Kepuasan & Keluhan Pasien</p>
                </div>
            </div>
        </div>

        <div class="dashboard-content">
            <h2 class="content-title">
                <i class="fas fa-tasks"></i> Pilih Layanan
            </h2>
            <p class="content-description">
                Kami menghargai setiap masukan Anda. Pilih salah satu opsi di bawah ini untuk melanjutkan:
            </p>

            <div class="options-container">
                <!-- Option 1: Survey Kepuasan -->
                <div class="option-card">
                    <div class="option-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="option-title">Survey Kepuasan</h3>
                    <p class="option-description">
                        Kumpulan data dan statistik <br> survey kepuasan pasien.
                    </p>
                    <a href="laporan-kepuasan.php" class="btn-option btn-survey">
                        <i class="fas fa-chart-line"></i> Lihat Detail Kepuasan
                    </a>
                </div>

                <!-- Option 2: Keluhan & Saran -->
                <div class="option-card">
                    <div class="option-icon">
                        <i class="fas fa-comment-alt"></i>
                    </div>
                    <h3 class="option-title">Keluhan & Saran</h3>
                    <p class="option-description">
                        Seluruh data keluhan <br> dan saran dari pasien.
                    </p>
                    <a href="laporan-keluhan.php" class="btn-option btn-complaint">
                        <i class="fas fa-envelope"></i> Lihat Detail Keluhan
                    </a>
                </div>
            </div>

            <div class="info-section">
                <h3 class="info-title">
                    <i class="fas fa-info-circle"></i> Informasi Penting
                </h3>
                <div class="info-content">
                    <p>
                        <strong>Survey Kepuasan:</strong> Survey ini bertujuan untuk mengetahui tingkat kepuasan pasien terhadap pelayanan kami.
                    </p>
                    <p style="margin-top: 15px;">
                        <strong>Keluhan & Saran:</strong> Gunakan form ini untuk menyampaikan keluhan, kritik, atau saran Anda kepada manajemen rumah sakit.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add hover effect sound (optional)
        document.querySelectorAll('.option-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-10px)';
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
            });
        });

        // Add click animation
        document.querySelectorAll('.btn-option').forEach(button => {
            button.addEventListener('click', function(e) {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });
    </script>
</body>

</html>