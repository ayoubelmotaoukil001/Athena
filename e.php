// ======================= views/dashboard.php =======================
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
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
                <button onclick="showProfile()" class="text-white hover:bg-indigo-700 px-3 py-2 rounded-md">
                    <i class="fas fa-user-circle mr-2"></i><?php echo $_SESSION['user_nom']; ?>
                </button>
                <button onclick="logout()" class="text-white hover:bg-indigo-700 px-3 py-2 rounded-md">
                    <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                </button>
            </div>
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="bg-white rounded-lg shadow-md mb-8 p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Projets</h2>
            <button onclick="openProjectModal()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i>Créer Projet
            </button>
        </div>
        <div id="projectsContainer" class="grid grid-cols-1 md:grid-cols-3 gap-4">Chargement...</div>
    </div>

    <div class="bg-white rounded-lg shadow-md mb-8 p-6 hidden" id="sprintsSection">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Sprints - <span id="selectedProjectName"></span></h2>
            <button onclick="openSprintModal()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>Créer Sprint
            </button>
        </div>
        <div id="sprintsContainer" class="grid grid-cols-1 md:grid-cols-3 gap-4">Chargement...</div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 hidden" id="tasksSection">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Tâches - <span id="selectedSprintName"></span></h2>
            <button onclick="openTaskModal()" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                <i class="fas fa-plus mr-2"></i>Créer Tâche
            </button>
        </div>
        <div id="tasksContainer" class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Fin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="tasksTable" class="bg-white divide-y divide-gray-200"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Profile -->
<div id="profileModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Mon Profil</h3>
            <button onclick="closeModal('profileModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="profileForm">
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Nom</label>
                <input type="text" id="profileNom" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Email</label>
                <input type="email" id="profileEmail" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Rôle</label>
                <input type="text" id="profileRole" readonly class="w-full px-3 py-2 border rounded-lg bg-gray-100">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Nouveau Mot de passe (optionnel)</label>
                <input type="password" id="profilePassword" class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal('profileModal')" class="px-4 py-2 bg-gray-300 rounded-lg">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Projet -->
<div id="projectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Créer un Projet</h3>
            <button onclick="closeModal('projectModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="projectForm">
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Titre</label>
                <input type="text" id="projectTitre" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Description</label>
                <textarea id="projectDescription" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">État</label>
                <select id="projectEtat" class="w-full px-3 py-2 border rounded-lg">
                    <option value="actif">Actif</option>
                    <option value="inactif">Inactif</option>
                </select>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal('projectModal')" class="px-4 py-2 bg-gray-300 rounded-lg">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Créer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Sprint -->
<div id="sprintModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Créer un Sprint</h3>
            <button onclick="closeModal('sprintModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="sprintForm">
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Nom</label>
                <input type="text" id="sprintNom" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Date Début</label>
                <input type="date" id="sprintDateDebut" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Date Fin</label>
                <input type="date" id="sprintDateFin" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal('sprintModal')" class="px-4 py-2 bg-gray-300 rounded-lg">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg">Créer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Task -->
<div id="taskModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Créer une Tâche</h3>
            <button onclick="closeModal('taskModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="taskForm">
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Titre</label>
                <input type="text" id="taskTitle" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Description</label>
                <textarea id="taskDescription" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Statut</label>
                <select id="taskStatus" class="w-full px-3 py-2 border rounded-lg">
                    <option value="a_faire">À faire</option>
                    <option value="en_cours">En cours</option>
                    <option value="terminee">Terminée</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Date Fin</label>
                <input type="date" id="taskDateFin" class="w-full px-3 py-2 border rounded-lg">
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal('taskModal')" class="px-4 py-2 bg-gray-300 rounded-lg">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg">Créer</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentProjectId = null;
let currentSprintId = null;

// ========== PROFILE FUNCTIONS (UML) ==========
async function showProfile() {
    try {
        const res = await fetch('../api/user.php?action=showProfile');
        const data = await res.json();
        
        if(data.success) {
            document.getElementById('profileNom').value = data.data.nom;
            document.getElementById('profileEmail').value = data.data.email;
            document.getElementById('profileRole').value = data.data.role;
            openModal('profileModal');
        }
    } catch(e) {
        console.error(e);
    }
}

document.getElementById('profileForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = {
        nom: document.getElementById('profileNom').value,
        email: document.getElementById('profileEmail').value,
        password: document.getElementById('profilePassword').value || null
    };
    
    try {
        const res = await fetch('../api/user.php?action=updateProfile', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(formData)
        });
        
        const data = await res.json();
        
        if(data.success) {
            alert('Profil mis à jour!');
            closeModal('profileModal');
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    } catch(e) {
        alert('Erreur de connexion');
        console.error(e);
    }
});

async function logout() {
    if(!confirm('Voulez-vous vous déconnecter?')) return;
    
    try {
        const res = await fetch('../api/auth.php?action=logout', {method: 'POST'});
        const data = await res.json();
        
        if(data.success) {
            window.location.href = 'login.php';
        }
    } catch(e) {
        console.error(e);
    }
}

// ========== PROJECTS ==========
async function loadProjects() {
    const container = document.getElementById('projectsContainer');
    container.innerHTML = 'Chargement...';
    
    try {
        const res = await fetch('../api/search.php?action=projects');
        const data = await res.json();
        
        if(!data.success || !data.data || data.data.length === 0) {
            container.innerHTML = '<div class="col-span-3 text-center text-gray-500">Aucun projet</div>';
            return;
        }

        container.innerHTML = '';
        data.data.forEach(p => {
            container.innerHTML += `
                <div onclick="selectProject(${p.projet_id}, '${p.titre}')" 
                     class="p-4 border rounded-lg cursor-pointer hover:bg-indigo-50 hover:border-indigo-300 transition">
                    <h3 class="font-bold text-lg">${p.titre}</h3>
                    <p class="text-sm text-gray-500">${p.description || 'Pas de description'}</p>
                    <span class="inline-block mt-2 px-2 py-1 text-xs rounded ${p.etat === 'actif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                        ${p.etat}
                    </span>
                </div>
            `;
        });
    } catch(e) {
        container.innerHTML = '<div class="col-span-3 text-center text-red-500">Erreur de connexion</div>';
        console.error(e);
    }
}

function selectProject(id, titre) {
    currentProjectId = id;
    document.getElementById('selectedProjectName').textContent = titre;
    document.getElementById('sprintsSection').classList.remove('hidden');
    document.getElementById('tasksSection').classList.add('hidden');
    loadSprints(id);
}

document.getElementById('projectForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = {
        titre: document.getElementById('projectTitre').value,
        description: document.getElementById('projectDescription').value,
        etat: document.getElementById('projectEtat').value,
        chef_id: <?php echo $_SESSION['user_id']; ?>
    };
    
    try {
        const res = await fetch('../api/project.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(formData)
        });
        
        const data = await res.json();
        
        if(data.success) {
            alert('Projet créé!');
            closeModal('projectModal');
            document.getElementById('projectForm').reset();
            loadProjects();
        } else {
            alert('Erreur: ' + data.message);
        }
    } catch(e) {
        alert('Erreur de connexion');
        console.error(e);
    }
});

// ========== SPRINTS ==========
async function loadSprints(projectId) {
    const container = document.getElementById('sprintsContainer');
    container.innerHTML = 'Chargement...';
    
    try {
        const res = await fetch(`../api/search.php?action=sprints&project_id=${projectId}`);
        const data = await res.json();
        
        if(!data.success || !data.data || data.data.length === 0) {
            container.innerHTML = '<div class="col-span-3 text-center text-gray-500">Aucun sprint</div>';
            return;
        }

        container.innerHTML = '';
        data.data.forEach(s => {
            container.innerHTML += `
                <div onclick="selectSprint(${s.sprintid}, '${s.nom}')" 
                     class="p-4 border rounded-lg cursor-pointer hover:bg-green-50 hover:border-green-300 transition">
                    <h3 class="font-bold text-lg">${s.nom}</h3>
                    <p class="text-sm text-gray-500">${s.date_debut} → ${s.date_fin}</p>
                </div>
            `;
        });
    } catch(e) {
        container.innerHTML = '<div class="col-span-3 text-center text-red-500">Erreur de connexion</div>';
        console.error(e);
    }
}

function selectSprint(id, nom) {
    currentSprintId = id;
    document.getElementById('selectedSprintName').textContent = nom;
    document.getElementById('tasksSection').classList.remove('hidden');
    loadTasks(id);
}

document.getElementById('sprintForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    if(!currentProjectId) {
        alert('Sélectionnez un projet d\'abord');
        return;
    }
    
    const formData = {
        nom: document.getElementById('sprintNom').value,
        date_debut: document.getElementById('sprintDateDebut').value,
        date_fin: document.getElementById('sprintDateFin').value,
        projet_id: currentProjectId
    };
    
    try {
        const res = await fetch('../api/sprint.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(formData)
        });
        
        const data = await res.json();
        
        if(data.success) {
            alert('Sprint créé!');
            closeModal('sprintModal');
            document.getElementById('sprintForm').reset();
            loadSprints(currentProjectId);
        } else {
            alert('Erreur: ' + data.message);
        }
    } catch(e) {
        alert('Erreur de connexion');
        console.error(e);
    }
});

// ========== TASKS ==========
async function loadTasks(sprintId) {
    const tbody = document.getElementById('tasksTable');
    tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Chargement...</td></tr>';
    
    try {
        const res = await fetch(`../api/tasks.php?sprint_id=${sprintId}`);
        const data = await res.json();
        
        if(!data.success || !data.data || data.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucune tâche</td></tr>';
            return;
        }

        tbody.innerHTML = '';
        data.data.forEach(t => {
            const statusClass = {
                'a_faire': 'bg-blue-100 text-blue-800',
                'en_cours': 'bg-yellow-100 text-yellow-800',
                'terminee': 'bg-green-100 text-green-800'
            }[t.status] || 'bg-gray-100';

            tbody.innerHTML += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-semibold">${t.title}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${t.description || '-'}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusClass}">
                            ${t.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">${t.date_fin || '-'}</td>
                    <td class="px-6 py-4">
                        <button onclick="deleteTask(${t.task_id})" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Erreur de connexion</td></tr>';
        console.error(e);
    }
}

document.getElementById('taskForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    if(!currentSprintId) {
        alert('Sélectionnez un sprint d\'abord');
        return;
    }
    
    const formData = {
        title: document.getElementById('taskTitle').value,
        description: document.getElementById('taskDescription').value,
        status: document.getElementById('taskStatus').value,
        date_fin: document.getElementById('taskDateFin').value || null,
        sprint_id: currentSprintId,
        user_id: <?php echo $_SESSION['user_id']; ?>
    };
    
    try {
        const res = await fetch('../api/tasks.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(formData)
        });
        
        const data = await res.json();
        
        if(data.success) {
            alert('Tâche créée!');
            closeModal('taskModal');
            document.getElementById('taskForm').reset();
            loadTasks(currentSprintId);
        } else {
            alert('Erreur: ' + data.message);
        }
    } catch(e) {
        alert('Erreur de connexion');
        console.error(e);
    }
});

async function deleteTask(taskId) {
    if(!confirm('Supprimer cette tâche?')) return;
    
    try {
        const res = await fetch('../api/tasks.php', {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({task_id: taskId})
        });
        
        const data = await res.json();
        
        if(data.success) {
            loadTasks(currentSprintId);
        } else {
            alert('Erreur: ' + data.message);
        }
    } catch(e) {
        alert('Erreur de connexion');
        console.error(e);
    }
}

// ========== MODAL HELPERS ==========
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function openProjectModal() {
    openModal('projectModal');
}

function openSprintModal() {
    if(!currentProjectId) {
        alert('Sélectionnez un projet d\'abord');
        return;
    }
    openModal('sprintModal');
}

function openTaskModal() {
    if(!currentSprintId) {
        alert('Sélectionnez un sprint d\'abord');
        return;
    }
    openModal('taskModal');
}

window.onload = loadProjects;
</script>
</body>
</html>