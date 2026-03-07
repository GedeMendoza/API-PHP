<?php
// =====================================================
// ARCHIVO: categoria.php
// DESCRIPCIÓN: API REST para tabla `categoria`
// ENDPOINTS:
//   GET    /categoria.php         → listar todas
//   GET    /categoria.php?id=1    → obtener una
//   POST   /categoria.php         → crear nueva
//   PUT    /categoria.php?id=1    → actualizar
//   DELETE /categoria.php?id=1    → eliminar
// =====================================================

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php';

$metodo = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

// ── GET ──────────────────────────────────────────────
if ($metodo === 'GET') {
    if ($id) {
        // Obtener una categoría por ID
        $stmt = $conn->prepare("SELECT * FROM categoria WHERE id_categoria = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        echo $resultado
            ? json_encode($resultado)
            : json_encode(["error" => "Categoría no encontrada"]);
    } else {
        // Listar todas las categorías
        $resultado = $conn->query("SELECT * FROM categoria ORDER BY id_categoria");
        $lista = [];
        while ($fila = $resultado->fetch_assoc()) {
            $lista[] = $fila;
        }
        echo json_encode($lista);
    }
}

// ── POST (Crear) ─────────────────────────────────────
elseif ($metodo === 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true);

    if (empty($datos['descripcion'])) {
        http_response_code(400);
        echo json_encode(["error" => "El campo 'descripcion' es obligatorio"]);
        exit();
    }

    $desc = trim($datos['descripcion']);
    $stmt = $conn->prepare("INSERT INTO categoria (descripcion) VALUES (?)");
    $stmt->bind_param("s", $desc);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode([
            "mensaje"      => "Categoría creada correctamente",
            "id_categoria" => $conn->insert_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al crear categoría"]);
    }
}

// ── PUT (Actualizar) ──────────────────────────────────
elseif ($metodo === 'PUT') {
    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "Se requiere el parámetro 'id'"]);
        exit();
    }

    $datos = json_decode(file_get_contents("php://input"), true);

    if (empty($datos['descripcion'])) {
        http_response_code(400);
        echo json_encode(["error" => "El campo 'descripcion' es obligatorio"]);
        exit();
    }

    $desc = trim($datos['descripcion']);
    $stmt = $conn->prepare("UPDATE categoria SET descripcion = ? WHERE id_categoria = ?");
    $stmt->bind_param("si", $desc, $id);

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Categoría actualizada correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al actualizar categoría"]);
    }
}

// ── DELETE (Eliminar) ─────────────────────────────────
elseif ($metodo === 'DELETE') {
    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "Se requiere el parámetro 'id'"]);
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM categoria WHERE id_categoria = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Categoría eliminada correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al eliminar categoría"]);
    }
}

$conn->close();
