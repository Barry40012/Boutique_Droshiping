<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;
use PDOException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        // Gérer les erreurs de connexion à la base de données
        if ($e instanceof PDOException || $e instanceof QueryException) {
            $message = $e->getMessage();
            
            // Vérifier si c'est une erreur de connexion
            if (
                str_contains($message, 'could not translate host name') ||
                str_contains($message, 'Unknown host') ||
                str_contains($message, 'Connection refused') ||
                str_contains($message, 'Connection timed out') ||
                str_contains($message, 'SQLSTATE[08006]') ||
                str_contains($message, 'SQLSTATE[HY000]')
            ) {
                // Retourner la vue d'erreur personnalisée
                return response()->view('errors.database', [], 503);
            }
        }

        return parent::render($request, $e);
    }
}
