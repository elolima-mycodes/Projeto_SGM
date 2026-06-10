<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Autorização: apenas solicitante
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'solicitante') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Acesso negado. Apenas solicitantes podem salvar chamados."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Método não permitido."]);
    exit;
}

// Receber e sanear dados
$id_solicitante = $_SESSION['user_id'];
$id_ambiente = isset($_POST['id_ambiente']) ? (int)$_POST['id_ambiente'] : 0;
$id_tipo_servico = isset($_POST['id_tipo']) ? (int)$_POST['id_tipo'] : 0;
$descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';

// Validar campos obrigatórios
if ($id_ambiente <= 0 || $id_tipo_servico <= 0 || empty($descricao)) {
    echo json_encode(["success" => false, "message" => "Preencha todos os campos obrigatórios (Ambiente, Tipo de Serviço e Descrição)."]);
    exit;
}

// Iniciar transação para garantir integridade
$conn->begin_transaction();

try {
    // Inserir chamado
    $status = 'aberto';
    $prioridade = 'baixa'; // padrão ao abrir
    
    $stmt = $conn->prepare("INSERT INTO chamados (id_solicitante, id_ambiente, id_tipo_servico, descricao_problema, status, prioridade) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisss", $id_solicitante, $id_ambiente, $id_tipo_servico, $descricao, $status, $prioridade);
    
    if (!$stmt->execute()) {
        throw new Exception("Erro ao inserir o chamado: " . $stmt->error);
    }
    
    $id_chamado = $conn->insert_id;
    
    // Processar foto (anexo)
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto']['tmp_name'];
        $fileName = $_FILES['foto']['name'];
        $fileSize = $_FILES['foto']['size'];
        $fileType = $_FILES['foto']['type'];
        
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        // Extensões permitidas
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception("Extensão de arquivo não permitida. Apenas imagens (jpg, jpeg, png, gif, webp) são aceitas.");
        }
        
        // Pasta de destino
        $uploadFileDir = '../assets/uploads/';
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }
        
        // Nome único para o arquivo
        $newFileName = 'abertura_' . uniqid() . '.' . $fileExtension;
        $dest_path = $uploadFileDir . $newFileName;
        
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Salvar no banco
            $caminho_arquivo = 'assets/uploads/' . $newFileName;
            $tipo_anexo = 'abertura';
            
            $stmtAnexo = $conn->prepare("INSERT INTO chamados_anexos (caminho_arquivo, tipo_anexo, id_chamado) VALUES (?, ?, ?)");
            $stmtAnexo->bind_param("ssi", $caminho_arquivo, $tipo_anexo, $id_chamado);
            
            if (!$stmtAnexo->execute()) {
                throw new Exception("Erro ao salvar anexo no banco de dados: " . $stmtAnexo->error);
            }
        } else {
            throw new Exception("Houve um erro ao mover o arquivo enviado para o diretório de uploads.");
        }
    }
    
    // Commit se tudo correu bem
    $conn->commit();
    echo json_encode(["success" => true, "id_chamado" => $id_chamado]);
    
} catch (Exception $e) {
    // Rollback em caso de erro
    $conn->rollback();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
