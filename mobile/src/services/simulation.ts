import apiClient from '@/api/client';

export interface Scenario {
    id: number;
    title: string;
    description: string;
    objective: any;
    complexity: string;
    initial_patient_state: any;
}

export interface SimulationSession {
    id: number;
    clinical_scenario_id: number;
    status: string;
    start_time: string;
    scenario?: Scenario;
    turns?: ConversationTurn[];
}

export interface ConversationTurn {
    id: number;
    role: 'user' | 'ai';
    content: string;
    created_at: string;
    reasoning_trace?: any[];
}

export const simulationService = {
    async getScenarios(page = 1): Promise<any> {
        const response = await apiClient.get(`scenarios?page=${page}`);
        return response.data;
    },

    async getScenario(id: number): Promise<Scenario> {
        const response = await apiClient.get(`scenarios/${id}`);
        return response.data;
    },

    async startSimulation(scenarioId: number): Promise<SimulationSession> {
        const response = await apiClient.post('simulations/start', { scenario_id: scenarioId });
        return response.data;
    },

    async getSimulation(id: number): Promise<SimulationSession> {
        const response = await apiClient.get(`simulations/${id}`);
        return response.data;
    },

    async sendChat(simulationId: number, message: string): Promise<{ user_turn: ConversationTurn; ai_turn: ConversationTurn; reasoning_trace?: any[] }> {
        const response = await apiClient.post(`simulations/${simulationId}/chat`, { message });
        return response.data;
    },

    async generateScenario(params: { type: string; difficulty: string; role: string; description?: string }): Promise<Scenario> {
        const response = await apiClient.post('/scenarios/generate', params);
        return response.data.data;
    },

    async getLastClinicalQuery(): Promise<any> {
        const response = await apiClient.get('/simulations/clinical-query/last');
        return response.data;
    },

    async sendClinicalQuery(formData: FormData): Promise<any> {
        const response = await apiClient.post('/simulations/clinical-query', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        return response.data;
    },

    async getProfile(): Promise<any> {
        const response = await apiClient.get('/profile');
        return response.data;
    },

    async updateProfile(data: any): Promise<any> {
        const response = await apiClient.put('/profile', data);
        return response.data;
    },

    async uploadAvatar(file: File): Promise<any> {
        const formData = new FormData();
        formData.append('avatar', file);
        const response = await apiClient.post('/profile/avatar', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        return response.data;
    }
};
