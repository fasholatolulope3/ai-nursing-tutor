<template>
  <IonPage>
    <IonHeader>
      <IonToolbar color="primary">
        <IonTitle>Clinical Workspace</IonTitle>
        <IonButtons slot="end">
          <IonButton @click="messages = []">
            <IonIcon slot="icon-only" :icon="refreshCircle"></IonIcon>
          </IonButton>
        </IonButtons>
      </IonToolbar>
    </IonHeader>
    <IonContent class="ion-padding">
      
      <!-- Chat Area -->
      <div class="messages-container">
        <div 
          v-for="(msg, i) in messages" 
          :key="i"
          :class="['message', msg.role]"
        >
          <div class="message-content">
            <IonText>{{ msg.content }}</IonText>
          </div>
        </div>
        
        <div v-if="isThinking" class="thinking ion-text-center ion-padding">
          <IonSpinner name="dots" color="primary"></IonSpinner>
          <p><small>Analyzing Clinical Data...</small></p>
        </div>
      </div>

      <!-- Clinical Labs Card -->
      <IonCard v-if="activeDocument" class="ion-margin-top">
        <IonCardHeader>
          <IonCardTitle class="ion-text-sm">
            <IonIcon :icon="medical" color="danger"></IonIcon>
            Clinical Context
          </IonCardTitle>
        </IonCardHeader>
        <IonCardContent>
          <IonList lines="none">
            <IonItem v-for="(fact, f) in activeDocument.facts" :key="f">
              <IonLabel class="ion-text-wrap text-sm">
                • {{ fact.text }}
              </IonLabel>
            </IonItem>
          </IonList>
        </IonCardContent>
      </IonCard>

    </IonContent>

    <!-- Footer Input -->
    <div class="chat-input-area ion-padding">
      <div class="input-wrapper">
        <IonInput 
          v-model="newMessage"
          placeholder="Describe assessment..."
          @keyup.enter="handleSendMessage"
        ></IonInput>
        <IonButton fill="clear" @click="handleSendMessage" :disabled="!newMessage.trim()">
          <IonIcon slot="icon-only" :icon="sendOutline"></IonIcon>
        </IonButton>
      </div>
    </div>
  </IonPage>
</template>

<style scoped>
.messages-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 20px;
}

.message {
  max-width: 85%;
  padding: 10px 14px;
  border-radius: 16px;
  font-size: 14px;
  line-height: 1.4;
}

.assistant {
  align-self: flex-start;
  background: var(--ion-color-light);
  border-bottom-left-radius: 4px;
}

.user {
  align-self: flex-end;
  background: var(--ion-color-primary);
  color: white;
  border-bottom-right-radius: 4px;
}

.chat-input-area {
  background: var(--ion-background-color);
  border-top: 1px solid var(--ion-color-light);
}

.input-wrapper {
  display: flex;
  align-items: center;
  background: var(--ion-color-light);
  border-radius: 24px;
  padding-left: 16px;
}

.text-sm {
  font-size: 0.85rem;
}
</style>

<script setup lang="ts">
import { 
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent, 
  IonButtons, IonButton, IonIcon, IonCard, IonCardHeader, 
  IonCardTitle, IonCardContent, IonList, IonItem, IonLabel,
  IonInput, IonSpinner, IonNote, IonAvatar, IonText
} from '@ionic/vue';
import { sendOutline, medical, documentText, refreshCircle } from 'ionicons/icons';
import { ref, onMounted } from 'vue';
import { simulationService } from '@/services/simulation';

// --- State Management ---
const isThinking = ref(false);
const activeDocument = ref<any>(null);
const messages = ref<any[]>([
  {
    role: 'assistant',
    content: 'Hello, nurse. I am ready to assist with your clinical reasoning. Please describe the patient case.',
    timestamp: new Date(),
  },
]);

const newMessage = ref('');

// --- Actions ---
const restoreInteraction = (interaction: any) => {
  const aiData = interaction.ai_response;
  messages.value.push({
    role: 'user',
    content: interaction.prompt,
    timestamp: new Date(interaction.created_at),
  });
  messages.value.push({
    role: 'assistant',
    content: aiData.answer,
    timestamp: new Date(interaction.created_at),
  });

  if (aiData.extracted_data) {
    activeDocument.value = {
      name: 'Restored Context',
      facts: Object.entries(aiData.extracted_data).map(([key, value]) => ({
        text: `${key}: ${value}`
      }))
    };
  }
};

const handleSendMessage = async () => {
  if (!newMessage.value.trim() || isThinking.value) return;

  const content = newMessage.value;
  newMessage.value = '';
  
  messages.value.push({
    role: 'user',
    content: content,
    timestamp: new Date(),
  });

  isThinking.value = true;

  try {
    const formData = new FormData();
    formData.append('message', content);

    const data = await simulationService.sendClinicalQuery(formData);

    messages.value.push({
      role: 'assistant',
      content: data.answer,
      timestamp: new Date(),
    });

    if (data.extracted_data) {
      activeDocument.value = {
        name: 'Clinical Analysis',
        facts: Object.entries(data.extracted_data).map(([key, value]) => ({
          text: `${key}: ${value}`
        }))
      };
    }
  } catch (error) {
    console.error('Query failed:', error);
  } finally {
    isThinking.value = false;
  }
};

onMounted(async () => {
  try {
    const data = await simulationService.getLastClinicalQuery();
    if (data) restoreInteraction(data);
  } catch (e) {
    console.error('No previous session');
  }
});
</script>
