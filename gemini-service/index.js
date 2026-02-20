const http = require('http');
const fs = require('fs');
const path = require('path');

// Load .env manually to avoid 'dotenv' dependency
let env = {};
try {
    const envPath = path.join(__dirname, '../.env');
    const envContent = fs.readFileSync(envPath, 'utf8');
    envContent.split('\n').forEach(line => {
        const [key, ...value] = line.split('=');
        if (key && value) {
            env[key.trim()] = value.join('=').trim().replace(/^["']|["']$/g, '');
        }
    });
} catch (e) {
    console.error('Error loading .env:', e.message);
}

const API_KEY = process.env['GEMINI_API_KEY'] || env['GEMINI_API_KEY'];
const PORT = 3001;

if (!API_KEY) {
    console.error('CRITICAL: GEMINI_API_KEY not found in process.env or .env');
    // Don't exit immediately on local dev if we might be using process.env
    if (process.env.NODE_ENV === 'production') process.exit(1);
}

const server = http.createServer(async (req, res) => {
    // Basic CORS
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

    if (req.method === 'OPTIONS') {
        res.writeHead(204);
        res.end();
        return;
    }

    if (req.method === 'POST') {
        let body = '';
        req.on('data', chunk => { body += chunk.toString(); });
        req.on('end', async () => {
            try {
                const data = JSON.parse(body);
                let responseData;

                if (req.url === '/analyze') {
                    console.log(`[${new Date().toISOString()}] Incoming /analyze request`);
                    responseData = await handleAnalyze(data);
                } else if (req.url === '/generate-scenario') {
                    console.log(`[${new Date().toISOString()}] Incoming /generate-scenario request`);
                    responseData = await handleScenario(data);
                } else if (req.url === '/simulation-turn') {
                    console.log(`[${new Date().toISOString()}] Incoming /simulation-turn request`);
                    responseData = await handleSimulation(data);
                } else {
                    console.log(`[${new Date().toISOString()}] Not Found: ${req.url}`);
                    res.writeHead(404);
                    res.end(JSON.stringify({ error: 'Not Found' }));
                    return;
                }

                res.writeHead(200, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify(responseData));
            } catch (error) {
                console.error('Request Error:', error);
                res.writeHead(500, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ error: error.message }));
            }
        });
    } else {
        res.writeHead(405);
        res.end(JSON.stringify({ error: 'Method Not Allowed' }));
    }
});

async function callGemini(model, systemInstruction, userPrompt, attachment = null, temperature = 0.7) {
    const url = `https://generativelanguage.googleapis.com/v1beta/models/${model}:generateContent?key=${API_KEY}`;
    
    const parts = [{ text: userPrompt }];
    if (attachment && attachment.data && attachment.mimeType) {
        parts.push({
            inlineData: {
                data: attachment.data,
                mimeType: attachment.mimeType
            }
        });
    }

    const payload = {
        contents: [{ role: 'user', parts: parts }],
        systemInstruction: { parts: [{ text: systemInstruction }] },
        generationConfig: {
            temperature: temperature,
            responseMimeType: "application/json",
        }
    };

    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 90000);

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
            signal: controller.signal
        });

        clearTimeout(timeoutId);

        if (!response.ok) {
            const errText = await response.text();
            throw new Error(`Gemini API error (${response.status}): ${errText}`);
        }

        const result = await response.json();
        const rawText = result.candidates[0].content.parts[0].text;
        return JSON.parse(rawText.replace(/^```json\s*|\s*```$/g, ''));
    } catch (err) {
        clearTimeout(timeoutId);
        if (err.name === 'AbortError') {
            throw new Error('Gemini API request timed out after 30s');
        }
        throw err;
    }
}

async function handleAnalyze({ message, previousSignature, attachment }) {
    const system = "You are the 'Clinical Context' AI Nursing Tutor. Output JSON.";
    const user = message + (previousSignature ? `\n[Context]: ${previousSignature}` : '');
    return callGemini('gemini-3-flash-preview', system, user, attachment, 0.4);
}

async function handleScenario({ type, difficulty, role, description }) {
    const system = "You are a Clinical Scenario Factory. Output JSON only.";
    const user = `Generate a ${difficulty} ${type} scenario for a ${role}. ${description || ''}`;
    return callGemini('gemini-3-flash-preview', system, user, 0.7);
}

async function handleSimulation({ scenarioTitle, complexity, initialPatientState, history, message }) {
    const system = `You are a Clinical Simulation Engine. CASE: ${scenarioTitle}`;
    const user = `History: ${JSON.stringify(history)}\nUser: ${message}`;
    return callGemini('gemini-3-flash-preview', system, user, 0.7);
}

server.listen(PORT, () => {
    console.log(`Standalone Gemini Proxy running at http://localhost:${PORT}`);
});
