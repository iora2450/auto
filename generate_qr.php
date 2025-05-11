<?php
// Incluir la biblioteca QRcode
include 'QRcode/qrlib.php';

// Función para generar el código QR y devolverlo como imagen PNG
function generateQRCode($text) {
    // Ruta y nombre de archivo temporal para guardar el código QR
    $tempFileName = 'temp_qrcode.png';

    // Generar el código QR y guardarlo en el archivo temporal
    QRcode::png($text, $tempFileName, QR_ECLEVEL_L, 10);

    // Leer el contenido del archivo temporal
    $qrCodeImage = file_get_contents($tempFileName);

    // Eliminar el archivo temporal
    unlink($tempFileName);

    // Devolver el contenido de la imagen como respuesta
    return $qrCodeImage;
}

// Obtener el texto para generar el código QR desde la solicitud
$texto = $_GET['texto'];

// Generar el código QR
$imagenQR = generateQRCode($texto);

// Establecer las cabeceras de la respuesta como una imagen PNG
header('Content-Type: image/png');

// Imprimir la imagen del código QR
echo $imagenQR;
