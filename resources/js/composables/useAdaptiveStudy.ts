import { ref, readonly } from 'vue';

const currentTopics = ref<string[]>([]);
const isRecommendationsActive = ref(false);

export function useAdaptiveStudy() {
    
    const setTopics = (topics: string[]) => {
        currentTopics.value = topics;
        isRecommendationsActive.value = topics.length > 0;
    };

    const clearTopics = () => {
        currentTopics.value = [];
        isRecommendationsActive.value = false;
    };

    return {
        currentTopics: readonly(currentTopics),
        isRecommendationsActive: readonly(isRecommendationsActive),
        setTopics,
        clearTopics
    };
}
