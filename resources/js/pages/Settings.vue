<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { User as UserIcon, Mail, Save, Camera, Loader2, LogOut } from 'lucide-vue-next';

const page = usePage();
const user = page.props.auth.user;

interface SettingsForm {
    name: string;
    email: string;
    bio: string;
    preferences: Record<string, any>;
    avatar: File | null;
}

const form = useForm<SettingsForm>({
    name: user.name,
    email: user.email,
    bio: (user.bio as string) || '',
    preferences: user.preferences || {},
    avatar: null,
});

const avatarPreview = ref<string | null>(user.avatar_path ? `/storage/${user.avatar_path}` : null);
const verificationMessage = ref('');

const handleAvatarChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        form.avatar = file; // Update form data
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);

        // Upload immediately or wait for save? 
        // Let's do immediate upload for avatar as is common, or wait. 
        // For simplicity with useForm, we can submit a separate request or use one.
        // Let's use a separate method for avatar to use the specific endpoint we made.
        uploadAvatar(file);
    }
};

const uploadAvatar = async (file: File) => {
    const formData = new FormData();
    formData.append('avatar', file);

    try {
        // We use axios directly for this multipart upload to checking api
        // But since we are using Inertia, we might want to stay within Inertia or use axios.
        // Let's use the form helper for profile update, but for avatar let's use a custom axios call or a separate inertia form.
        // To catch up with the backend API I built: POST /api/v1/profile/avatar
        // I should use axios since it's an API endpoint, not an Inertia visit.
        
        const axios = (await import('axios')).default;
        const response = await axios.post('/api/v1/profile/avatar', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        // Update page prop or force reload? 
        // Inertia won't automatically know. We can manually update the page prop if we want.
        page.props.auth.user.avatar_path = response.data.avatar_url.replace('/storage/', ''); // Hacky path fix
        verificationMessage.value = 'Avatar updated!';
        setTimeout(() => verificationMessage.value = '', 3000);
        
    } catch (error) {
        console.error('Avatar upload failed', error);
    }
};

const submitProfile = () => {
    // We use Axios to call our API endpoint: PUT /api/v1/profile
    // Inertia form usually targets a backend route that returns an Inertia response.
    // BUT, my backend implementation in ProfileController returns JSON.
    // So I should use axios, not form.put (which expects Inertia response).
    
    // Changing strategy: Use axios for the API calls since I built a JSON API.
    updateProfile();
};

const isSubmitting = ref(false);

const updateProfile = async () => {
    isSubmitting.value = true;
    const axios = (await import('axios')).default;
    try {
        await axios.put('/api/v1/profile', {
            name: form.name,
            email: form.email,
            bio: form.bio,
            preferences: form.preferences
        });
        
        // Update local state to reflect changes immediately
        page.props.auth.user.name = form.name;
        page.props.auth.user.email = form.email;
        page.props.auth.user.bio = form.bio;
        page.props.auth.user.preferences = form.preferences;

        verificationMessage.value = 'Profile updated successfully!';
        setTimeout(() => verificationMessage.value = '', 3000);
        
    } catch (error) {
        console.error('Profile update failed', error);
    } finally {
        isSubmitting.value = false;
    }
};

</script>

<template>
    <Head title="Account Settings" />
    
    <DashboardLayout title="Settings" description="Manage your account preferences">
        <div class="max-w-2xl mx-auto space-y-6">
            
            <!-- Avatar Section -->
            <div class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl p-6 flex flex-col items-center sm:flex-row sm:items-start gap-6">
                <div class="relative group">
                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 dark:bg-white/10 ring-4 ring-white dark:ring-white/5">
                        <img 
                            v-if="avatarPreview" 
                            :src="avatarPreview" 
                            class="w-full h-full object-cover" 
                            alt="Avatar" 
                        />
                         <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                             <User class="w-10 h-10" />
                         </div>
                    </div>
                    <label class="absolute inset-0 flex items-center justify-center bg-black/50 text-white opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer rounded-full">
                        <Camera class="w-6 h-6" />
                        <input type="file" class="hidden" accept="image/*" @change="handleAvatarChange" />
                    </label>
                </div>
                
                <div class="flex-1 text-center sm:text-left">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Profile Picture</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Upload a professional photo for your clinical profile.
                    </p>
                    <p class="text-xs text-gray-400 mt-2">JPG, GIF or PNG. Max size 2MB.</p>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Personal Information</h3>
                
                <form @submit.prevent="submitProfile" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                            <div class="relative">
                                <UserIcon class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" />
                                <input 
                                    v-model="form.name"
                                    type="text" 
                                    class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 dark:border-white/10 bg-transparent text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 font-sans"
                                    placeholder="Jane Doe"
                                />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                            <div class="relative">
                                <Mail class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" />
                                <input 
                                    v-model="form.email"
                                    type="email" 
                                    class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 dark:border-white/10 bg-transparent text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                                    placeholder="nurse@example.com"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Clinical Bio</label>
                        <textarea 
                            v-model="form.bio"
                            rows="4"
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 dark:border-white/10 bg-transparent text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 resize-none"
                            placeholder="Share your nursing background, specialties, and learning goals..."
                        ></textarea>
                         <p class="text-xs text-gray-500 text-right">{{ form.bio?.length || 0 }}/1000</p>
                    </div>

                    <div class="pt-4 flex items-center justify-between">
                         <span v-if="verificationMessage" class="text-sm text-emerald-600 font-medium">
                            {{ verificationMessage }}
                        </span>
                        <span v-else></span>

                        <button 
                            type="submit" 
                            :disabled="isSubmitting"
                            class="flex items-center gap-2 px-6 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg transition-colors disabled:opacity-50"
                        >
                            <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
                            <Save v-else class="w-4 h-4" />
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Danger Zone -->
            <div class="bg-white dark:bg-white/5 border border-red-200 dark:border-red-900/50 rounded-xl p-6">
                <h3 class="text-lg font-medium text-red-600 dark:text-red-400 mb-2">Account Actions</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    Sign out of your account on this device.
                </p>
                
                <div class="flex items-center">
                    <button 
                        @click="router.post('/logout')"
                        class="flex items-center gap-2 px-6 py-2 border border-red-200 dark:border-red-900/50 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/10 rounded-lg transition-colors font-medium"
                    >
                        <LogOut class="w-4 h-4" />
                        Log Out
                    </button>
                </div>
            </div>
            
        </div>
    </DashboardLayout>
</template>
