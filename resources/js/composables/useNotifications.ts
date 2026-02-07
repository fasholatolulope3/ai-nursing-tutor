import { ref } from 'vue';
import axios from 'axios';

const notifications = ref<any[]>([]);
const unreadCount = ref(0);
const isLoading = ref(false);

export function useNotifications() {
    
    const fetchNotifications = async () => {
        isLoading.value = true;
        try {
            const response = await axios.get('/api/v1/notifications');
            notifications.value = response.data.notifications;
            unreadCount.value = response.data.unread_count;
        } catch (error) {
            console.error('Failed to fetch notifications:', error);
        } finally {
            isLoading.value = false;
        }
    };

    const markAsRead = async (id: string) => {
        try {
            await axios.put(`/api/v1/notifications/${id}/read`);
            // Optimistically update UI
            const notification = notifications.value.find(n => n.id === id);
            if (notification && !notification.read_at) {
                notification.read_at = new Date().toISOString();
                unreadCount.value = Math.max(0, unreadCount.value - 1);
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    };

    const markAllAsRead = async () => {
        try {
            await axios.put('/api/v1/notifications/read-all');
            notifications.value.forEach(n => n.read_at = new Date().toISOString());
            unreadCount.value = 0;
        } catch (error) {
            console.error('Failed to mark all as read:', error);
        }
    };

    return {
        notifications,
        unreadCount,
        isLoading,
        fetchNotifications,
        markAsRead,
        markAllAsRead
    };
}
