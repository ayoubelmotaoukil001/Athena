<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Athena - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-indigo-500 to-purple-600 min-h-screen flex items-center justify-center">
    
    <div class="bg-white rounded-lg shadow-2xl p-8 w-96">
        <div class="text-center mb-6">
            <i class="fas fa-project-diagram text-indigo-600 text-5xl mb-3"></i>
            <h1 class="text-3xl font-bold text-gray-800">Athena</h1>
            <p class="text-gray-500">Gestion de Projets</p>
        </div>

        <!-- Error Alert (hidden by default) -->
        <div id="errorAlert" class="hidden mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span id="errorMessage">Erreur de connexion</span>
            </div>
        </div>

        <!-- Success Alert (hidden by default) -->
        <div id="successAlert" class="hidden mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span id="successMessage">Connexion réussie!</span>
            </div>
        </div>

        <form id="loginForm" class="space-y-4">
            <div>
                <label for="loginEmail" class="block text-gray-700 font-semibold mb-2">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </span>
                    <input type="email" id="loginEmail" required 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="admin@athena.com"
                        autocomplete="email">
                </div>
            </div>
            
            <div>
                <label for="loginPassword" class="block text-gray-700 font-semibold mb-2">Mot de passe</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-lock text-gray-400"></i>
                    </span>
                    <input type="password" id="loginPassword" required 
                        class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="••••••••"
                        autocomplete="current-password">
                    <button type="button" id="togglePassword" 
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" id="loginButton"
                class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition font-semibold flex items-center justify-center">
                <i class="fas fa-sign-in-alt mr-2"></i>
                <span id="buttonText">Se connecter</span>
            </button>
        </form>

        <div class="mt-4 text-center text-sm text-gray-500">
            <p class="mb-1">Compte de test:</p>
            <p class="font-mono text-xs bg-gray-50 p-2 rounded">
                admin@athena.com / admin123
            </p>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center text-xs text-gray-400">
            <p>&copy; 2024 Athena Project Management</p>
        </div>
    </div>

    <script>
    // ========== TOGGLE PASSWORD VISIBILITY ==========
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('loginPassword');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    });

    // ========== ALERT FUNCTIONS ==========
    function showAlert(type, message) {
        const errorAlert = document.getElementById('errorAlert');
        const successAlert = document.getElementById('successAlert');
        
        // Hide both alerts first
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

    // ========== BUTTON STATE MANAGEMENT ==========
    function setButtonLoading(loading) {
        const button = document.getElementById('loginButton');
        const buttonText = document.getElementById('buttonText');
        
        if (loading) {
            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');
            buttonText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Connexion...';
        } else {
            button.disabled = false;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
            buttonText.innerHTML = 'Se connecter';
        }
    }

    // ========== API CALL HELPER ==========
    async function makeAPICall(url, data) {
        try {
            console.log('Making API call to:', url);
            console.log('With data:', data);
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);

            // Check if response is actually JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Response is not JSON:', text);
                throw new Error('Le serveur n\'a pas retourné de JSON. Vérifiez le fichier API.');
            }

            const result = await response.json();
            console.log('Response data:', result);
            
            return result;
        } catch (error) {
            console.error('API Call Error:', error);
            throw error;
        }
    }

    // ========== LOGIN FORM HANDLER ==========
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        hideAlerts();
        setButtonLoading(true);
        
        const email = document.getElementById('loginEmail').value.trim();
        const password = document.getElementById('loginPassword').value;

        // Basic validation
        if (!email || !password) {
            showAlert('error', 'Veuillez remplir tous les champs');
            setButtonLoading(false);
            return;
        }

        // Email format validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showAlert('error', 'Format d\'email invalide');
            setButtonLoading(false);
            return;
        }

        try {
            const data = await makeAPICall('../api/auth.php?action=login', {
                email: email,
                password: password
            });
            
            if (data.success) {
                showAlert('success', 'Connexion réussie! Redirection...');
                
                // Wait 1 second before redirect for better UX
                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 1000);
            } else {
                showAlert('error', data.message || 'Email ou mot de passe incorrect');
                setButtonLoading(false);
            }
        } catch (error) {
            console.error('Login error:', error);
            
            let errorMessage = 'Erreur de connexion au serveur';
            
            if (error.message.includes('JSON')) {
                errorMessage = 'Erreur serveur: Réponse invalide. Vérifiez le fichier auth.php';
            } else if (error.message.includes('Failed to fetch')) {
                errorMessage = 'Impossible de contacter le serveur. Vérifiez que le serveur est démarré.';
            }
            
            showAlert('error', errorMessage);
            setButtonLoading(false);
        }
    });

    // ========== AUTO-FILL FOR TESTING (Optional - Remove in production) ==========
    // Uncomment the lines below for easier testing
    /*
    window.addEventListener('DOMContentLoaded', () => {
        document.getElementById('loginEmail').value = 'admin@athena.com';
        document.getElementById('loginPassword').value = 'admin123';
    });
    */

    // ========== CLEAR ALERTS ON INPUT ==========
    document.getElementById('loginEmail').addEventListener('input', hideAlerts);
    document.getElementById('loginPassword').addEventListener('input', hideAlerts);

    console.log('Login page loaded and ready');
    </script>
</body>
</html>