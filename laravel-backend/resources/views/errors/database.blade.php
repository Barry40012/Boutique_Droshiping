<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion indisponible - Dropshipping Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: #333;
        }

        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 3rem;
            text-align: center;
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .error-icon i {
            font-size: 4rem;
            color: white;
        }

        .error-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .error-message {
            font-size: 1.1rem;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .error-details {
            background: #f9fafb;
            border-left: 4px solid #667eea;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            text-align: left;
        }

        .error-details-title {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .error-details-title i {
            color: #667eea;
        }

        .error-details-list {
            list-style: none;
            padding: 0;
        }

        .error-details-list li {
            padding: 0.5rem 0;
            color: #6b7280;
            display: flex;
            align-items: start;
            gap: 0.75rem;
        }

        .error-details-list li i {
            color: #10b981;
            margin-top: 0.25rem;
            flex-shrink: 0;
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        @media (max-width: 640px) {
            .error-container {
                padding: 2rem 1.5rem;
            }

            .error-title {
                font-size: 1.5rem;
            }

            .error-icon {
                width: 100px;
                height: 100px;
            }

            .error-icon i {
                font-size: 3rem;
            }

            .error-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-database"></i>
        </div>
        
        <h1 class="error-title">Connexion indisponible</h1>
        
        <p class="error-message">
            Nous rencontrons actuellement des difficultés à nous connecter à notre base de données.
            <br>
            Veuillez réessayer dans quelques instants.
        </p>

        <div class="error-details">
            <div class="error-details-title">
                <i class="fas fa-info-circle"></i>
                <span>Que faire ?</span>
            </div>
            <ul class="error-details-list">
                <li>
                    <i class="fas fa-check-circle"></i>
                    <span>Vérifiez votre connexion Internet</span>
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <span>Attendez quelques secondes et actualisez la page</span>
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <span>Si le problème persiste, contactez le support</span>
                </li>
            </ul>
        </div>

        <div class="error-actions">
            <a href="javascript:location.reload()" class="btn btn-primary">
                <i class="fas fa-sync-alt"></i>
                <span>Réessayer</span>
            </a>
            <a href="{{ url('/') }}" class="btn btn-secondary">
                <i class="fas fa-home"></i>
                <span>Retour à l'accueil</span>
            </a>
        </div>
    </div>
</body>
</html>

