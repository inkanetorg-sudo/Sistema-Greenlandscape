<?php

class MarketingController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Método para mostrar el panel de marketing
    public function index() {
        require_once __DIR__ . '/../views/admin/marketing.php';
    }

    // Lógica para enviar el aviso de temporada a todos
    public function avisoTemporada() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $asunto = $_POST['asunto'] ?? 'Comunicado Importante';
            $mensaje = $_POST['mensaje'] ?? '';
            
            // Comprobar si se subió un archivo
            $archivo = $_FILES['archivo_adjunto'] ?? null;
            $tiene_adjunto = ($archivo && $archivo['error'] == UPLOAD_ERR_OK);

            // Obtener correos
            $query = "SELECT email FROM clientes WHERE email IS NOT NULL AND email != ''";
            $clientes = $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);

            // Preparar el remitente (puedes cambiarlo al correo real de Dennis)
            $remitente = "info@greenvillagelandscape.com";

            foreach ($clientes as $c) {
                $para = $c['email'];

                if ($tiene_adjunto) {
                    // Lógica para enviar CON adjunto
                    $nombre_archivo = $archivo['name'];
                    $tipo_archivo = $archivo['type'];
                    $ruta_temporal = $archivo['tmp_name'];
                    
                    // Leer y codificar el archivo
                    $contenido = file_get_contents($ruta_temporal);
                    $contenido_codificado = chunk_split(base64_encode($contenido));
                    
                    // Crear un separador único
                    $separador = md5(time());
                    
                    // Cabeceras del correo (MIME)
                    $cabeceras = "From: " . $remitente . "\r\n";
                    $cabeceras .= "MIME-Version: 1.0\r\n";
                    $cabeceras .= "Content-Type: multipart/mixed; boundary=\"" . $separador . "\"\r\n";
                    
                    // Cuerpo del mensaje (Texto)
                    $cuerpo = "--" . $separador . "\r\n";
                    $cuerpo .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
                    $cuerpo .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                    $cuerpo .= $mensaje . "\r\n\r\n";
                    
                    // Cuerpo del mensaje (Archivo)
                    $cuerpo .= "--" . $separador . "\r\n";
                    $cuerpo .= "Content-Type: " . $tipo_archivo . "; name=\"" . $nombre_archivo . "\"\r\n";
                    $cuerpo .= "Content-Disposition: attachment; filename=\"" . $nombre_archivo . "\"\r\n";
                    $cuerpo .= "Content-Transfer-Encoding: base64\r\n\r\n";
                    $cuerpo .= $contenido_codificado . "\r\n\r\n";
                    $cuerpo .= "--" . $separador . "--";
                    
                    mail($para, $asunto, $cuerpo, $cabeceras);
                } else {
                    // Lógica para enviar SIN adjunto (Solo texto)
                    $cabeceras = "From: " . $remitente . "\r\n";
                    $cabeceras .= "Content-Type: text/html; charset=UTF-8\r\n";
                    mail($para, $asunto, nl2br($mensaje), $cabeceras);
                }
            }
            
            // Registrar en el log
            $stmt = $this->db->prepare("INSERT INTO marketing_logs (tipo_envio, asunto) VALUES ('temporada', ?)");
            $stmt->execute([$asunto]);
            
            header('Location: /greenland/index.php?action=marketing&status=enviado');
            exit;
        }
    }
	
	public function filtrarClientesPorServicio() {
        $id_servicio = $_POST['id_servicio'] ?? 0;
        
        $query = "SELECT DISTINCT c.nombre_completo, c.email 
                  FROM clientes c 
                  WHERE c.id_cliente NOT IN (
                      SELECT f.id_cliente 
                      FROM facturas f
                      JOIN factura_detalles fd ON f.id_factura = fd.id_factura
                      WHERE fd.id_producto = ?
                  )";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_servicio]);
        $clientesFiltrados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Llamamos a una nueva vista específica para ventas
        require_once __DIR__ . '/../views/admin/marketing_resultados.php';
    }
}