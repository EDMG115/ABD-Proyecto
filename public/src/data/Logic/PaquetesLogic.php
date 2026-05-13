<?php
    require_once "./../DAO/paqueteDAO.php";
    require_once "./../Util/seguridad.php";


    header('Content-Type: application/json');
    $paqueteDAO = new paqueteDAO();
    $RUTA_IMG_ESTANDAR = "./../../media/images/lugares/";
    $RUTA_FISICA_GUARDADO = __DIR__ . "/../../media/images/lugares/";
    

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        
        if (!verificarPermisos("ver_lugar")) {
            redirigirAlIndex();
        }

        $id_lugar = $_GET["id_lugar"];
        
        try{
            $paquetes = $paqueteDAO->getPaquetesPorLugar($id_lugar);
            
            /*if($paquetes != null){
                
                foreach ($paquetes as &$paquete) {
                    $paquete['imagen_url'] = $RUTA_IMG_ESTANDAR . $paquete['imagen_url'];
                }
                $respuesta = ['correcto' => true, 'lugares' => $paquetes];
            } else {
                $respuesta = ['correcto' => false];
            }*/
            $respuesta = ['correcto' => true, 'paquetes' => $paquetes];
            echo json_encode($respuesta);
        } catch (Exception $e){
            $respuesta = ['correcto' => false, 'mensaje' => 'Error - ' . $e->getMessage()];
            echo json_encode($respuesta);
        }
    } 