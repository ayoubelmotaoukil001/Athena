<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_nom'] = 'Admin';
    $_SESSION['user_role'] = 'admin';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Athena - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

<nav class="bg-indigo-600 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <i class="fas fa-project-diagram text-white text-2xl mr-3"></i>
                <span class="text-white text-2xl font-bold">Athena</span>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-white">
                    <i class="fas fa-user-circle mr-2"></i>
                    <?php echo $_SESSION['user_nom']; ?>
                </span>
            </div>
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" id="searchTitle" placeholder="Rechercher..." class="border border-gray-300 rounded-lg px-4 py-2">
            
            <select id="filterStatus" class="border border-gray-300 rounded-lg px-4 py-2">
                <option value="">Tous les statuts</option>
                <option value="a_faire">À faire</option>
                <option value="en_cours">En cours</option>
                <option value="terminee">Terminée</option>
            </select>
            
            <button onclick="searchTasks()" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                <i class="fas fa-search mr-2"></i>Rechercher
            </button>
            
            <button onclick="showCreateModal()" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-plus mr-2"></i>Nouvelle Tâche
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Liste des Tâches</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date fin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="tasksTable" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Chargement...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div id="pagination" class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
        </div>
    </div>

</div>

<div id="createModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Nouvelle Tâche</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form onsubmit="createTask(event)">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Titre</label>
                <input type="text" id="taskTitle" class="w-full px-3 py-2 border rounded-md" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea id="taskDescription" class="w-full px-3 py-2 border rounded-md" rows="3"></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Statut</label>
                <select id="taskStatus" class="w-full px-3 py-2 border rounded-md">
                    <option value="a_faire">À faire</option>
                    <option value="en_cours">En cours</option>
                    <option value="terminee">Terminée</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Sprint ID</label>
                <input type="number" id="taskSprintId" class="w-full px-3 py-2 border rounded-md" value="1" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Date fin</label>
                <input type="date" id="taskDateFin" class="w-full px-3 py-2 border rounded-md">
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded-md">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Créer</button>
            </div>
        </form>
    </div>
</div>

<script>
async function loadTasks(page = 1) {
    try {
        const response = await fetch(`tasks.php?page=${page}`);
        const result = await response.json();
        
        console.log('Tasks loaded:', result);
        
        if (result.success) {
            displayTasks(result.data.data);
            displayPagination(result.data);
        } else {
            document.getElementById('tasksTable').innerHTML = 
                `<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Erreur: ${result.message}</td></tr>`;
        }
    } catch (error) {
        console.error('Erreur:', error);
        document.getElementById('tasksTable').innerHTML = 
            `<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Erreur de connexion: ${error.message}</td></tr>`;
    }
}

async function searchTasks() {
    const title = document.getElementById('searchTitle').value;
    const status = document.getElementById('filterStatus').value;
    
    try {
        const response = await fetch(`tasks.php?search=1&title=${title}&status=${status}`);
        const result = await response.json();
        
        if (result.success) {
            displayTasks(result.data);
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur: ' + error.message);
    }
}

function displayTasks(tasks) {
    const tbody = document.getElementById('tasksTable');
    
    if (!tasks || tasks.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucune tâche trouvée</td></tr>';
        return;
    }
    
    tbody.innerHTML = '';
    
    tasks.forEach(task => {
        const statusClass = {
            'a_faire': 'bg-blue-100 text-blue-800',
            'en_cours': 'bg-yellow-100 text-yellow-800',
            'terminee': 'bg-green-100 text-green-800'
        }[task.status] || 'bg-gray-100 text-gray-800';
        
        const statusText = {
            'a_faire': 'À faire',
            'en_cours': 'En cours',
            'terminee': 'Terminée'
        }[task.status] || task.status;
        
        tbody.innerHTML += `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">${task.title}</td>
                <td class="px-6 py-4 text-sm text-gray-500">${task.description || '-'}</td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 text-xs font-semibold rounded-full ${statusClass}">
                        ${statusText}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm">${task.date_fin || '-'}</td>
                <td class="px-6 py-4 space-x-2">
                    <button onclick="deleteTask(${task.task_id})" class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
}

function displayPagination(data) {
    const pagination = document.getElementById('pagination');
    let html = `<div class="text-sm text-gray-700">
        Page ${data.page} sur ${data.totalPages} (${data.total} tâches)
    </div><div class="flex space-x-2">`;
    
    for (let i = 1; i <= data.totalPages; i++) {
        const active = i === data.page ? 'bg-indigo-600 text-white' : 'border border-gray-300 hover:bg-gray-100';
        html += `<button onclick="loadTasks(${i})" class="px-3 py-1 rounded-md ${active}">${i}</button>`;
    }
    
    html += '</div>';
    pagination.innerHTML = html;
}

async function createTask(event) {
    event.preventDefault();
    
    const data = {
        title: document.getElementById('taskTitle').value,
        description: document.getElementById('taskDescription').value,
        status: document.getElementById('taskStatus').value,
        date_fin: document.getElementById('taskDateFin').value,
        sprint_id: parseInt(document.getElementById('taskSprintId').value),
        user_id: <?php echo $_SESSION['user_id']; ?>
    };
    
    console.log('Creating task:', data);
    
    try {
        const response = await fetch('tasks.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        console.log('Create response:', result);
        
        if (result.success) {
            alert('Tâche créée avec succès!');
            closeModal();
            document.getElementById('taskTitle').value = '';
            document.getElementById('taskDescription').value = '';
            document.getElementById('taskDateFin').value = '';
            loadTasks();
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur: ' + error.message);
    }
}

async function deleteTask(taskId) {
    if (!confirm('Voulez-vous vraiment supprimer cette tâche?')) return;
    
    try {
        const response = await fetch('tasks.php', {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ task_id: taskId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Tâche supprimée!');
            loadTasks();
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur: ' + error.message);
    }
}

function showCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('createModal').classList.add('hidden');
}

window.onload = () => {
    loadTasks();
};
</script>

</body>
</html>