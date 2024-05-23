document.getElementById('fetchTasksButton').addEventListener('click', fetchTasks);
document.getElementById('addTaskButton').addEventListener('click', () => {
    const titleInput = document.getElementById('newTaskTitle');
    const prazoInput = document.getElementById('newTaskprazo');
    const title = titleInput.value;
    const prazo = prazoInput.value;
    addTask(title, prazo);
    titleInput.value = ''; //Limpa o campo de título
    prazoInput.value = ''; //Limpa o campo de prazo
});

async function fetchTasks() {
    try {
        const response = await fetch('http://localhost/testeBemol/api.php');
        const tasks = await response.json();
        if (tasks.length === 0) {
            alert('Não há tarefas para exibir.');
        } else {
            displayTasks(tasks);
        }
    } catch (error) {
        console.error('Erro ao buscar tarefas:', error);
    }
}

async function addTask(title, prazo) {
    if (!title || !prazo) {
        console.error('O título e o prazo da tarefa são obrigatórios');
        return;
    }

    try {
        const response = await fetch('http://localhost/testeBemol/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ title, prazo })
        });
        const newTask = await response.json();
        fetchTasks();
        alert('Tarefa adicionada com sucesso!');
    } catch (error) {
        console.error('Erro ao adicionar tarefa:', error);
    }
}

async function updateTask(taskId, newTitle, prazo, completed) {
    try {
        const response = await fetch(`http://localhost/testeBemol/api.php`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: taskId, title: newTitle, prazo: prazo, completed: completed })
        });
        const result = await response.json();
        if (result.error) {
            console.error('Erro ao atualizar tarefa:', result.error);
        } else {
            fetchTasks();
            alert('Tarefa atualizada com sucesso!');
        }
    } catch (error) {
        console.error('Erro ao atualizar tarefa:', error);
    }
}

async function deleteTask(taskId) {
    try {
        const response = await fetch(`http://localhost/testeBemol/api.php`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: taskId })
        });
        const result = await response.json();
        if (result.error) {
            console.error('Erro ao deletar tarefa:', result.error);
        } else {
            fetchTasks();
            alert('Tarefa deletada com sucesso!');
        }
    } catch (error) {
        console.error('Erro ao deletar tarefa:', error);
    }
}

function displayTasks(tasks) {
    const taskList = document.getElementById('taskList');
    const obs = document.getElementById('obs');
    taskList.innerHTML = '';
    obs.innerHTML = '<br><br><label for="newTaskTitle">Obs: ao fazer qualquer alteração deve clicar em "Up"</label>';

    //Ordena as tarefas pela data em ordem decrescente
    tasks.sort((a, b) => new Date(b.prazo) - new Date(a.prazo));

    tasks.forEach(task => {
        const listItem = document.createElement('li');

        const inputField = document.createElement('input');
        inputField.type = 'text';
        inputField.value = task.title;
        inputField.classList.add('edit-task-input');
        listItem.appendChild(inputField);

        const deadlineInput = document.createElement('input');
        const prazo = new Date(task.prazo);
        const year = prazo.getFullYear();
        const month = String(prazo.getMonth() + 1).padStart(2, '0');
        const day = String(prazo.getDate() + 1).padStart(2, '0');
        deadlineInput.type = 'date';
        deadlineInput.value = `${year}-${month}-${day}`; //Formato YYYY-MM-DD
        deadlineInput.classList.add('edit-deadline-input');
        listItem.appendChild(deadlineInput);

        const status = document.createElement('span');
        status.textContent = task.completed ? 'Feita' : 'Aberta';
        listItem.appendChild(status);

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.checked = task.completed;
        checkbox.addEventListener('change', () => {
            status.textContent = checkbox.checked ? 'Feita' : 'Aberta';
        });
        listItem.appendChild(checkbox);

        const updateButton = document.createElement('button');
        updateButton.classList.add('custom-button-up');
        updateButton.innerText = 'Up';
        updateButton.addEventListener('click', () => {
            const newTitle = inputField.value;
            const newPrazo = deadlineInput.value;
            const completed = checkbox.checked;
            updateTask(task.id, newTitle, newPrazo, completed);
        });
        listItem.appendChild(updateButton);

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('custom-button-x');
        deleteButton.innerText = 'X';
        deleteButton.addEventListener('click', () => deleteTask(task.id));
        listItem.appendChild(deleteButton);

        taskList.appendChild(listItem);
    });
}
