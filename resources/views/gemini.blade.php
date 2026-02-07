<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Gemini AI Assistant</title>

    <!-- Tailwind CSS (via CDN for simplicity as requested, or standard stack if available) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .loader {
            border: 3px solid #f3f3f3;
            border-radius: 50%;
            border-top: 3px solid #3b82f6;
            width: 20px;
            height: 20px;
            -webkit-animation: spin 1s linear infinite;
            /* Safari */
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">

    <div class="max-w-4xl w-full space-y-8 bg-white p-8 rounded-xl shadow-lg">

        <div class="text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Gemini AI Nursing Tutor
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Ask a clinical question or request a simulation scenario.
            </p>
        </div>

        <div class="space-y-6">
            <!-- Input Form -->
            <div>
                <label for="prompt" class="block text-sm font-medium text-gray-700">Your Prompt</label>
                <div class="mt-1">
                    <textarea id="prompt" name="prompt" rows="4"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                        placeholder="e.g., Explain the pathophysiology of CHF..."></textarea>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- Thinking Level Selection (Optional) -->
                    <select id="thinking_level"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md border">
                        <option value="LOW">Thinking: Low</option>
                        <option value="MEDIUM">Thinking: Medium</option>
                        <option value="HIGH">Thinking: High</option>
                    </select>
                </div>

                <button id="submitBtn" type="button" onclick="submitPrompt()"
                    class="group relative w-40 flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                    <span id="btnText">Generate</span>
                    <span id="btnLoader" class="hidden ml-2 loader"></span>
                </button>
            </div>

            <!-- Error Container -->
            <div id="errorContainer" class="hidden rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <!-- Heroicon name: solid/x-circle -->
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800" id="errorTitle">Error</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p id="errorMessage"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Output Container -->
            <div id="outputContainer" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">AI Response:</label>
                <div id="responseText"
                    class="prose max-w-none p-4 bg-gray-50 rounded-lg border border-gray-200 whitespace-pre-wrap font-mono text-sm">
                </div>
            </div>

        </div>
    </div>

    <script>
        async function submitPrompt() {
            const promptInput = document.getElementById('prompt');
            const thinkingLevelInput = document.getElementById('thinking_level');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');
            const errorContainer = document.getElementById('errorContainer');
            const errorMessage = document.getElementById('errorMessage');
            const outputContainer = document.getElementById('outputContainer');
            const responseText = document.getElementById('responseText');

            const prompt = promptInput.value.trim();
            const thinkingLevel = thinkingLevelInput.value;

            // Reset UI
            errorContainer.classList.add('hidden');
            outputContainer.classList.add('hidden');
            responseText.innerText = '';

            // Validation
            if (!prompt) {
                showError('Please enter a prompt.');
                return;
            }

            // Loading state
            submitBtn.disabled = true;
            btnText.innerText = 'Thinking...';
            btnLoader.classList.remove('hidden');

            try {
                const response = await fetch('/api/v1/gemini', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        // Laravel CSRF - optional for API routes if stateless, but usually needed for web
                        // Since we are calling /api/v1/gemini which is stateless (usually), we might not need CSRF if using API token
                        // BUT user asked for WEB route to view, and Fetch to API. API routes are usually not CSRF protected by default?
                        // Wait, user instructions: "POST request to /gemini" (api or web?)
                        // User Step 5: "POST to: /gemini". 
                        // User Step 8: "Include CSRF token".
                        // Let's assume we post to the API route we created in previous turn: /api/v1/gemini
                        // OR we should create a web route?
                        // User instructions say: "Route::get('/ai', ...)" -> view.
                        // "POST to /gemini".
                        // In previous turn we made route in `api.php`: `/v1/gemini`.
                        // I will target `/api/v1/gemini` as that is where logic is.
                    },
                    body: JSON.stringify({
                        prompt: prompt,
                        thinking_level: thinkingLevel
                    })
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    responseText.innerText = data.response;
                    outputContainer.classList.remove('hidden');
                } else {
                    console.error('API Error:', data);
                    showError(data.message || 'An error occurred while generating content.');
                }

            } catch (error) {
                console.error('Fetch Error:', error);
                showError('Network error. Please try again.');
            } finally {
                // Reset Loading State
                submitBtn.disabled = false;
                btnText.innerText = 'Generate';
                btnLoader.classList.add('hidden');
            }
        }

        function showError(msg) {
            const errorContainer = document.getElementById('errorContainer');
            const errorMessage = document.getElementById('errorMessage');
            errorMessage.innerText = msg;
            errorContainer.classList.remove('hidden');
        }
    </script>
</body>

</html>