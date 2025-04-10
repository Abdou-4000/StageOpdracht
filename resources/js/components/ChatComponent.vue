<template>
    <div class="chat-container">
      <div class="messages" ref="messagesContainer">
        <div v-for="message in messages" :key="message.id" class="message">
          <strong>{{ message.user.name }}:</strong> {{ message.message }}
          <small class="timestamp">{{ formatTimestamp(message.created_at) }}</small>
        </div>
      </div>
      <div class="input-area">
        <input
          type="text"
          v-model="newMessage"
          @keyup.enter="sendMessage"
          placeholder="Type je bericht..."
          :disabled="sending"
        />
        <button @click="sendMessage" :disabled="!newMessage || sending">
          {{ sending ? 'Verzenden...' : 'Verstuur' }}
        </button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, nextTick } from 'vue';
  
  // Refs voor data
  const messages = ref([]);
  const newMessage = ref('');
  const sending = ref(false);
  const messagesContainer = ref(null); // Ref voor de message container div
  
  // Functie om bestaande berichten op te halen
  const fetchMessages = async () => {
    try {
      const storedMessages = localStorage.getItem('chat_messages');
      messages.value = storedMessages ? JSON.parse(storedMessages) : [];
      scrollToBottom();
    } catch (error) {
      console.error('Error fetching messages from localStorage:', error);
    }
  };
  
  // Functie om een nieuw bericht te versturen
  const sendMessage = async () => {
    if (!newMessage.value.trim() || sending.value) return;
    
    sending.value = true;
    try {
      const newMessageObj = {
        id: Date.now(),
        message: newMessage.value,
        created_at: new Date().toISOString(),
        user: {
          name: 'Test User', // Hardcoded user for testing
          id: 1
        }
      };
  
      messages.value.push(newMessageObj);
      localStorage.setItem('chat_messages', JSON.stringify(messages.value));
      
      newMessage.value = '';
      scrollToBottom();
  
      // Simulate broadcast to other users
      window.Echo.channel('public-chat').trigger('client-message', {
        message: newMessageObj
      });
  
    } catch (error) {
      console.error('Error saving message to localStorage:', error);
    } finally {
      sending.value = false;
    }
  };
  
  // Functie om bericht toe te voegen (wordt aangeroepen door Echo listener)
  const addMessage = (messageData) => {
    messages.value.push(messageData);
    scrollToBottom();
  };
  
  // Functie om naar beneden te scrollen
  const scrollToBottom = () => {
    // Wacht tot de DOM is bijgewerkt voordat je scrollt
    nextTick(() => {
      if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
      }
    });
  };
  
  // Functie om timestamp te formatteren (simpel voorbeeld)
  const formatTimestamp = (timestamp) => {
    if (!timestamp) return '';
    const date = new Date(timestamp);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  };
  
  
  // Lifecycle hook: wordt uitgevoerd als de component is gemount
  onMounted(() => {
    console.log('Chat component mounted');
    fetchMessages();
  
    window.Echo.channel('public-chat')
      .listenForWhisper('client-message', (event) => {
        console.log('Message received from other client:', event);
        addMessage(event.message);
      });
  
     // Optioneel: Luister naar connectie status van Echo/Pusher
      window.Echo.connector.pusher.connection.bind('connected', () => {
          console.log('Pusher connected!');
      });
      window.Echo.connector.pusher.connection.bind('disconnected', () => {
          console.log('Pusher disconnected!');
      });
      window.Echo.connector.pusher.connection.bind('error', (err) => {
          console.error('Pusher connection error:', err);
      });
  });
  
  </script>
  
  <style scoped>
  .chat-container {
    display: flex;
    flex-direction: column;
    height: 400px; /* Of een andere gewenste hoogte */
    border: 1px solid #ccc;
    background-color: #f9f9f9;
  }
  
  .messages {
    flex-grow: 1;
    overflow-y: auto;
    padding: 10px;
    border-bottom: 1px solid #ccc;
    background-color: white;
  }
  
  .message {
    margin-bottom: 8px;
    padding: 5px 8px;
    background-color: #e9e9e9;
    border-radius: 4px;
    max-width: 80%;
  }
  
  .message strong {
      display: block;
      font-size: 0.9em;
      color: #333;
  }
  .message .timestamp {
      font-size: 0.7em;
      color: #777;
      margin-left: 10px;
  }
  
  .input-area {
    display: flex;
    padding: 10px;
  }
  
  .input-area input {
    flex-grow: 1;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  
  .input-area button {
    margin-left: 10px;
    padding: 8px 15px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
   .input-area button:disabled {
      background-color: #aaa;
      cursor: not-allowed;
   }
  </style>