import apiClient from '@/lib/axios';

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
    }
};
