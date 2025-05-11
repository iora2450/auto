<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Mpdf\Mpdf; // Asegúrate de tener instalada la librería Mpdf

class CorreoController extends Controller
{
    public function enviarCorreoConImagenYTexto($destinatario, $asunto, $mensajeTexto, $json, $aqrl, $pdf, $isTest = false) {
        try {
            // Validar que el destinatario no esté vacío
            if (empty($destinatario)) {
                throw new Exception("Error: El destinatario está vacío.");
            }

            // Validar que el asunto no esté vacío
            if (empty($asunto)) {
                throw new Exception("Error: El asunto está vacío.");
            }

            // Validar que el mensaje no esté vacío
            if (empty($mensajeTexto)) {
                throw new Exception("Error: El mensaje de texto está vacío.");
            }

            // Decodificar el JSON
            $objetoJson = json_decode($json, true);
            if ($objetoJson === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Error: JSON inválido.");
            }

            // Agregar el JSON dentro de la etiqueta <pre> para preservar el formato
            $jsonFormatted = json_encode($objetoJson['dteJson'], JSON_PRETTY_PRINT);

            // Guarda el JSON en un archivo de texto
            $archivo = 'archivo.json';
            if (file_put_contents($archivo, $jsonFormatted) === false) {
                throw new Exception("Error: No se pudo guardar el archivo JSON.");
            }

            // URL de la imagen del QR
            $imagenQR = "https://rcsinversiones.com/demo/generate_qr.php?texto=" . urlencode($aqrl);

            // Preparar el mensaje de correo electrónico en HTML
            $mensajeHtml = "<html><body>{$mensajeTexto}<br><img src=\"{$imagenQR}\" alt=\"QR Code\" style=\"width: 200px; height: 200px;\"></body></html>";

            // Enviar el correo usando Laravel's Mail facade
            if (!$isTest) {
                Mail::send([], [], function ($message) use ($destinatario, $asunto, $mensajeHtml, $archivo, $pdf) {
                    $message->to($destinatario)
                            ->subject($asunto)
                            ->setBody($mensajeHtml, 'text/html')
                            ->attach($archivo, [
                                'as' => 'archivo.json',
                                'mime' => 'application/json',
                            ]);

                    // Adjuntar el PDF si no es null
                    if ($pdf !== null) {
                        $pdfData = $pdf->output();
                        $message->attachData($pdfData, 'consumidor.pdf', [
                            'mime' => 'application/pdf',
                        ]);
                    }
                });
            }

            // Eliminar el archivo JSON después de enviarlo
            if (!unlink($archivo)) {
                throw new Exception("Error: No se pudo eliminar el archivo JSON después de enviarlo.");
            }

            // Si todo sale bien
            return "Correo enviado exitosamente.";

        } catch (Exception $e) {
            // Captura cualquier excepción y devuelve el mensaje de error
            return $e->getMessage();
        }
    }

    public function probarEnvioCorreo() {
        $destinatario = 'iora2451@gmail.com';
        $asunto = 'Asunto de Prueba';
        $mensajeTexto = 'Este es un mensaje de prueba.';
        $json = '{"dteJson":{}}';
        $aqrl = 'Datos para el QR';
        
    
            $pdf = null;
        
        
        // Probar la función con modo de prueba activado (no enviará correo real)
        $resultado = $this->enviarCorreoConImagenYTexto($destinatario, $asunto, $mensajeTexto, $json, $aqrl, $pdf, true);
        
        return $resultado;
    }
}
