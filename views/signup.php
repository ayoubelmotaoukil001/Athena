<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Athena - Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-indigo-500 to-purple-600 min-h-screen flex items-center justify-center">

<div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md">
    <div class="text-center mb-6">
        <i class="fas fa-project-diagram text-indigo-600 text-5xl mb-3"></i>
        <h1 class="text-3xl font-bold text-gray-800">Athena</h1>
        <p class="text-gray-500">Créer un compte</p>
    </div>

    <!-- Alerts -->
    <div id="errorAlert" class="hidden mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span id="errorMessage"></span>
        </div>
    </div>

    <div id="successAlert" class="hidden mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span id="successMessage"></span>
        </div>
    </div>

    <form id="signupForm" class="space-y-4">
        <!-- Nom -->
        <div>
            <label for="nom" class="block text-gray-700 font-semibold mb-2">Nom complet</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-user text-gray-400"></i>
                </span>
                <input type="text" id="nom" placeholder="Jean Dupont" 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                    required>
            </div>
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-envelope text-gray-400"></i>
                </span>
                <input type="email" id="email" placeholder="jean@example.com" 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                    required>
            </div>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-gray-700 font-semibold mb-2">Mot de passe</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-lock text-gray-400"></i>
                </span>
                <input type="password" id="password" placeholder="••••••••" 
                    class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                    required minlength="6">
                <button type="button" id="togglePassword" 
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <p class="text-xs text-gray-500 mt-1">Minimum 6 caractères</p>
        </div>

        <!-- Role -->
        <div>
            <label for="role" class="block text-gray-700 font-semibold mb-2">Rôle</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-user-tag text-gray-400"></i>
                </span>
                <select id="role" 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                    required>
                    <option value="">Choisir un rôle</option>
                    <option value="membre">Membre</option>
                    <option value="projectchef">Chef de Projet</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" id="submitButton"
            class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition font-semibold">
            <span id="buttonText">
                <i class="fas fa-user-plus mr-2"></i>S'inscrire
            </span>
        </button>
    </form>

    <!-- Login Link -->
    <p class="text-sm text-gray-500 mt-4 text-center">
        Déjà un compte? 
        <a href="login.php" class="text-indigo-600 hover:underline font-semibold">Se connecter</a>
    </p>

    <!-- Footer -->
    <div class="mt-6 text-center text-xs text-gray-400">
        <p>&copy; 2024 Athena Project Management</p>
    </div>
</div>

<script>
// ========== PASSWORD TOGGLE ==========
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// ========== ALERT FUNCTIONS ==========
function showAlert(type, message) {
    const errorAlert = document.getElementById('errorAlert');
    const successAlert = document.getElementById('successAlert');
    
    errorAlert.classList.add('hidden');
    successAlert.classList.add('hidden');
    
    if (type === 'error') {
        document.getElementById('errorMessage').textContent = message;
        errorAlert.classList.remove('hidden');
    } else if (type === 'success') {
        document.getElementById('successMessage').textContent = message;
        successAlert.classList.remove('hidden');
    }
}

function hideAlerts() {
    document.getElementById('errorAlert').classList.add('hidden');
    document.getElementById('successAlert').classList.add('hidden');
}

// ========== BUTTON STATE ==========
function setButtonLoading(loading) {
    const button = document.getElementById('submitButton');
    const buttonText = document.getElementById('buttonText');
    
    if (loading) {
        button.disabled = true;
        button.classList.add('opacity-50', 'cursor-not-allowed');
        buttonText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Création du compte...';
    } else {
        button.disabled = false;
        button.classList.remove('opacity-50', 'cursor-not-allowed');
        buttonText.innerHTML = '<i class="fas fa-user-plus mr-2"></i>S\'inscrire';
    }
}

// ========== FORM SUBMISSION ==========
document.getElementById('signupForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    hideAlerts();
    setButtonLoading(true);
    
    const nom = document.getElementById('nom').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const role = document.getElementById('role').value;
    
    // Validation
    if (!nom || !email || !password || !role) {
        showAlert('error', 'Veuillez remplir tous les champs');
        setButtonLoading(false);
        return;
    }
    
    if (password.length < 6) {
        showAlert('error', 'Le mot de passe doit contenir au moins 6 caractères');
        setButtonLoading(false);
        return;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showAlert('error', 'Format d\'email invalide');
        setButtonLoading(false);
        return;
    }
    
    try {
        console.log('Sending signup request...');
        
        const response = await fetch('../api/auth.php?action=signup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                nom: nom,
                email: email,
                password: password,
                role: role
            })
        });
        
        console.log('Response status:', response.status);
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Non-JSON response:', text);
            throw new Error('Le serveur n\'a pas retourné de JSON valide');
        }
        
        const result = await response.json();
        console.log('Response data:', result);
        
        if (result.success) {
            showAlert('success', result.message || 'Compte créé avec succès! Redirection...');
            document.getElementById('signupForm').reset();
            
            // Redirect to login page after 2 seconds
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        } else {
            showAlert('error', result.message || 'Erreur lors de la création du compte');
            setButtonLoading(false);
        }
        
    } catch (error) {
        console.error('Signup error:', error);
        showAlert('error', error.message || 'Erreur de connexion au serveur');
        setButtonLoading(false);
    }
});

// Clear alerts on input
['nom', 'email', 'password', 'role'].forEach(id => {
    document.getElementById(id).addEventListener('input', hideAlerts);
});

console.log('Signup page loaded');
</script>

</body>
</html>