```html
<template>
  <IonPage>
    <IonHeader>
      <IonToolbar color="tertiary">
        <IonTitle>Simulation Case</IonTitle>
      </IonToolbar>
    </IonHeader>

    <IonContent class="ion-padding">
      <div v-if="loading" class="ion-text-center ion-padding">
        <IonSpinner name="crescent"></IonSpinner>
        <p>Initializing Patient State...</p>
      </div>

      <template v-else-if="session">
        <!-- Scenario Info -->
        <IonCard class="scenario-card">
          <IonCardHeader>
            <IonCardSubtitle>Active Assessment</IonCardSubtitle>
            <IonCardTitle>{{ session.scenario?.title }}</IonCardTitle>
          </IonCardHeader>
          <IonCardContent>
            <p>{{ session.scenario?.description }}</p>
          </IonCardContent>
        </IonCard>

        <!-- Vitals Grid -->
        <IonGrid>
          <IonRow>
            <IonCol size="6" v-for="(val, key) in session.scenario?.initial_patient_state?.vitals" :key="key">
              <IonCard class="vitals-card">
                <IonCardContent class="ion-text-center">
                  <IonNote class="vitals-label">{{ key }}</IonNote>
                  <div class="vitals-value">{{ val }}</div>
                </IonCardContent>
              </IonCard>
            </IonCol>
          </IonRow>
        </IonGrid>

        <!-- Chat turns -->
        <div class="chat-history">
          <div v-for="turn in session.turns" :key="turn.id" :class="['bubble', turn.role]">
            {{ turn.content }}
          </div>
        </div>
      </template>
    </IonContent>

    <IonFooter v-if="session">
      <div class="chat-input-area ion-padding">
        <div class="input-wrapper">
          <IonInput 
            v-model="newMessage"
            placeholder="Intervention..."
            @keyup.enter="handleSendMessage"
          ></IonInput>
          <IonButton fill="clear" @click="handleSendMessage" :disabled="!newMessage.trim() || sending">
            <IonIcon slot="icon-only" :icon="chatboxEllipses"></IonIcon>
          </IonButton>
        </div>
      </div>
    </IonFooter>
  </IonPage>
</template>

<script setup lang="ts">
import { 
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonGrid, IonRow, IonCol, IonCard, IonCardHeader, IonCardSubtitle,
  IonCardTitle, IonCardContent, IonBadge, IonIcon, IonList, IonItem,
  IonLabel, IonNote, IonSpinner, IonButton, IonFooter, IonInput
} from '@ionic/vue';
import { heart, thermometer, leaf, water, fitness, chatboxEllipses, megaphone } from 'ionicons/icons';
import { ref, onMounted } from 'vue';
import { simulationService, type SimulationSession } from '@/services/simulation';

const session = ref<SimulationSession | null>(null);
const loading = ref(true);
const newMessage = ref('');
const sending = ref(false);

const loadSession = async (id: number) => {
  try {
    session.value = await simulationService.getSimulation(id);
  } catch (e) {
    console.error('Failed to load simulation');
  } finally {
    loading.value = false;
  }
};

const handleSendMessage = async () => {
  if (!session.value || !newMessage.value.trim() || sending.value) return;
  
  const content = newMessage.value;
  newMessage.value = '';
  sending.value = true;

  try {
    const response = await simulationService.sendChat(session.value.id, content);
    session.value.turns?.push(response.user_turn, response.ai_turn);
  } catch (e) {
    console.error('Failed to send message');
  } finally {
    sending.value = false;
  }
};

onMounted(() => {
  // For demo/initial version, we'll try to find an active session
  // In a real app, this would come from a navigation param
  loadSession(1); // Placeholder ID
});
</script>

<style scoped>
.vitals-label {
  text-transform: uppercase;
  font-size: 10px;
  font-weight: bold;
}
.vitals-value {
  font-size: 20px;
  font-weight: bold;
  color: var(--ion-color-tertiary);
}
.chat-history {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 16px;
}
.bubble {
  max-width: 85%;
  padding: 8px 12px;
  border-radius: 12px;
  font-size: 13px;
}
.ai {
  align-self: flex-start;
  background: var(--ion-color-light);
}
.user {
  align-self: flex-end;
  background: var(--ion-color-tertiary);
  color: white;
}
.input-wrapper {
  display: flex;
  align-items: center;
  background: var(--ion-color-light);
  border-radius: 24px;
  padding-left: 16px;
}
</style>
```
