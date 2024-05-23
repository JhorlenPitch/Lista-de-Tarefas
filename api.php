<?php

require 'database.php';

//Rota para buscar todas as tarefas
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_GET)) {
    try {
        $stmt = $conn->query('SELECT * FROM tarefas');
        $tarefas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($tarefas);
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

//Rota para adicionar uma nova tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['title']) && empty($data['prazo'])) {
        echo json_encode(['error' => 'O título/prazo da tarefa é obrigatório']);
        exit;
    }

    $title = $data['title'];
    $prazo = $data['prazo'];

    try {
        $stmt = $conn->prepare('INSERT INTO tarefas (title, prazo) VALUES (:title, :prazo)');
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':prazo', $prazo);
        $stmt->execute();
        $taskId = $conn->lastInsertId();
        echo json_encode(['id' => $taskId, 'title' => $title, 'prazo' => $prazo, 'completed' => false]);
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

//Rota para atualizar uma tarefa
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['id'])) {
        echo json_encode(['error' => 'O ID da tarefa é obrigatório']);
        exit;
    }

    $taskId = $data['id'];
    $title = !empty($data['title']) ? $data['title'] : null;
    $completed = isset($data['completed']) ? (int)$data['completed'] : null;
    $prazo = !empty($data['prazo']) ? $data['prazo'] : null; // Adicionando o prazo

    try {
        //Verifica se tanto o título quanto o status de conclusão foram fornecidos
        if ($title !== null && $completed !== null && $prazo !== null) {
            $stmt = $conn->prepare('UPDATE tarefas SET title = :title, completed = :completed, prazo = :prazo WHERE id = :id');
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':completed', $completed);
            $stmt->bindParam(':prazo', $prazo); // Adicionando o prazo
        } elseif ($title !== null && $completed !== null) {
            $stmt = $conn->prepare('UPDATE tarefas SET title = :title, completed = :completed WHERE id = :id');
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':completed', $completed);
        } elseif ($title !== null && $prazo !== null) {
            $stmt = $conn->prepare('UPDATE tarefas SET title = :title, prazo = :prazo WHERE id = :id');
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':prazo', $prazo); // Adicionando o prazo
        } elseif ($completed !== null && $prazo !== null) {
            $stmt = $conn->prepare('UPDATE tarefas SET completed = :completed, prazo = :prazo WHERE id = :id');
            $stmt->bindParam(':completed', $completed);
            $stmt->bindParam(':prazo', $prazo); // Adicionando o prazo
        } elseif ($title !== null) {
            $stmt = $conn->prepare('UPDATE tarefas SET title = :title WHERE id = :id');
            $stmt->bindParam(':title', $title);
        } elseif ($completed !== null) {
            $stmt = $conn->prepare('UPDATE tarefas SET completed = :completed WHERE id = :id');
            $stmt->bindParam(':completed', $completed);
        } elseif ($prazo !== null) {
            $stmt = $conn->prepare('UPDATE tarefas SET prazo = :prazo WHERE id = :id');
            $stmt->bindParam(':prazo', $prazo); // Adicionando o prazo
        } else {
            echo json_encode(['error' => 'Nenhum dado para atualizar']);
            exit;
        }

        $stmt->bindParam(':id', $taskId);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}


//Rota para deletar uma tarefa
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['id'])) {
        echo json_encode(['error' => 'O ID da tarefa é obrigatório']);
        exit;
    }

    $taskId = $data['id'];

    try {
        $stmt = $conn->prepare('DELETE FROM tarefas WHERE id = :id');
        $stmt->bindParam(':id', $taskId);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}