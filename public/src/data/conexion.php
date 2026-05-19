<?php
date_default_timezone_set('America/Chihuahua');
class Conexion
{
    private $conexion;
    private $driver = "mysql";
    private $host = "localhost";
    private $database = "abdarcproyecto5";
    private $port = "3306";
    private $charset = "utf8mb4";

    // Matriz de credenciales según el rol
    private $credenciales = [
        'administrador' => [
            'username' => 'arc_admin',
            'password' => 'k9#Mv2!pL8$zQ1w'
        ],
        'organizadora' => [
            'username' => 'arc_org_eventos',
            'password' => 'rE6&hF3%vP0+gK4'
        ],
        'agencia' => [
            'username' => 'arc_org_viajes',
            'password' => 'T4@jN7*cX5^bY9m'
        ],
        'usuario' => [
            'username' => 'arc_cliente',
            'password' => 'W2$yA8!sD9#nJ5tX7@'
        ],
        // El usuario por defecto para visitantes que no han iniciado sesión
        // y para hacer la consulta de validación de Login.
        'invitado' => [
            'username' => 'arc_invitado',
            'password' => 'nE6&iZ2%vH0+gN9'
        ]
    ];

    public function conectar()
    {
        // Asegurarnos de que la sesión esté iniciada para poder leer $_SESSION
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Determinar qué rol tiene el usuario actual. Si no hay sesión, es 'invitado'
        $rolActual = $_SESSION['tipo_usuario'] ?? 'invitado';

        // Evitar errores si por alguna razón el rol en sesión no existe en nuestro array
        if (!array_key_exists($rolActual, $this->credenciales)) {
            $rolActual = 'invitado';
        }

        // Obtener el usuario y contraseña correctos para este rol
        $username = $this->credenciales[$rolActual]['username'];
        $password = $this->credenciales[$rolActual]['password'];

        $url = "{$this->driver}:host={$this->host};port={$this->port};dbname={$this->database};charset={$this->charset}";

        try {
            $this->conexion = new PDO($url, $username, $password);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            error_log("Conexión exitosa a DB usando el rol: " . $rolActual . " (User: $username)");

            return $this->conexion;
        } catch (PDOException $e) {
            throw new Exception("Error en la conexión de la base de datos: " . $e->getMessage());
        }
    }

    public function __destruct()
    {
        $this->conexion = NULL;
    }

    public function getConexion()
    {
        if ($this->conexion === NULL) {
            $this->conexion = $this->conectar();
        }
        return $this->conexion;
    }

    public function getCredencialesActuales()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tipo_usuario'])) {
            throw new Exception("No hay usuario en sesión");
        }

        $rol = $_SESSION['tipo_usuario'];

        if (!isset($this->credenciales[$rol])) {
            throw new Exception("Rol inválido");
        }

        return $this->credenciales[$rol];
    }
}
