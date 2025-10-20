<script setup>
import axios from "axios";
import { useRoute, useRouter } from "vue-router";
import { reactive } from "vue";
import Swal from "sweetalert2";
import { ref, onMounted } from "vue";

const router = useRouter();
const loading = ref(false);
const selectedFile = ref(null);
const profilePreview = ref(null);

const currentUser = ref({
    name: "",
    email: "",
});

const form = reactive({
    bio: "",
    location: "",
    website: "",
    github_url: "",
});

const fetchCurrentUser = async () => {
    try {
        const response = await axios.get("/api/profile/user");

        currentUser.value = response.data;
    } catch (error) {
        console.error("Failed to fetch data", error);
    }
};

const SetupProfile = async () => {
    if (!form.bio.trim() || !form.location.trim()) {
        await Swal.fire({
            icon: "error",
            title: "Missing required fields",
            text: "Please fill in all required fields",
        });
        return;
    }

    loading.value = true;

    try {
        const formData = new FormData();

        formData.append("bio", form.bio.trim());
        formData.append("location", form.location.trim());

        if (form.website.trim()) {
            formData.append("website", form.website.trim());
        }

        if (selectedFile.value) {
            formData.append("photo_profile", selectedFile.value);
        }
        if (form.github_url.trim()) {
            formData.append("github_url", form.github_url.trim());
        }

        const response = await axios.post("/api/profile/setup", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        await Swal.fire({
            icon: "success",
            title: "Success!",
            text: "Profile has been Setup",
            timer: 2000,
            showConfirmButton: true,
        });

        router.push("/home");
    } catch (error) {
        console.log("There Something wrong", error);

        let errorMessage = "Please Check the back-end for the error";

        if (error.response?.data?.errors) {
            const errors = error.response.data.errors;
            errorMessage = Object.values(errors).flat().join("");
        } else if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
        }

        await Swal.fire({
            icon: "error",
            title: "Error!",
            text: errorMessage,
            timer: 3000,
            showConfirmButton: true,
        });
    } finally {
        loading.value = false;
    }
};

const handleProfile = (event) => {
    const file = event.target.files[0];

    if (file) {
        if (!file.type.startsWith("image/")) {
            Swal.fire({
                icon: "error",
                title: "Not image",
                text: "Please Make sure image type",
                timer: 3000,
                showConfirmButton: true,
            });
            // this is for like reset once you submit not passed in validation
            event.target.value = "";
            return;
        }
    }

    // checking if or validation ng size ng image

    if (file.size > 5 * 1024 * 1024) {
        Swal.fire({
            icon: "error",
            title: "Error!",
            text: "Please select an image smaller than 5MB",
        });
        event.target.value = "";
        return;
    }

    selectedFile.value = file;

    const reader = new FileReader();
    reader.onload = (e) => {
        profilePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};
onMounted(() => {
    fetchCurrentUser();
});
</script>

<template>
    <body class="bg-[#0a0a0a] text-gray-100 min-h-screen">
        <header
            class="border-b border-gray-800 bg-black/50 backdrop-blur-sm sticky top-0 z-50"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-code text-2xl text-[#ff6b35]"></i>
                        <span class="text-xl font-bold">DevBlog</span>
                    </div>
                    <button class="text-gray-400 hover:text-gray-300 text-sm">
                        Skip for now
                    </button>
                </div>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold mb-4">Welcome to DevBlog! 👋</h1>
                <p class="text-gray-400 text-lg">
                    Let's set up your profile to get started
                </p>
            </div>

            <div class="step-content active" id="step-1">
                <div class="bg-[#161616] border border-gray-800 rounded-lg p-8">
                    <h2 class="text-2xl font-bold mb-6">Basic Information</h2>

                    <form @submit.prevent="SetupProfile">
                        <div class="mb-8 text-center">
                            <div class="mb-4 flex justify-center">
                                <div class="relative">
                                    <img
                                        :src="profilePreview"
                                        alt="Avatar"
                                        class="avatar-preview"
                                        id="avatar-preview"
                                    />
                                    <label
                                        for="avatar-upload"
                                        class="absolute bottom-0 right-0 bg-[#ff6b35] hover:bg-[#ff5722] w-10 h-10 rounded-full flex items-center justify-center cursor-pointer transition-colors"
                                    >
                                        <i class="fas fa-camera text-white"></i>
                                    </label>
                                    <input
                                        type="file"
                                        name="photo_profile"
                                        @change="handleProfile"
                                        id="avatar-upload"
                                        class="hidden"
                                        accept="image/*"
                                    />
                                </div>
                            </div>
                            <p class="text-sm text-gray-400">
                                Upload a profile picture
                            </p>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label
                                    class="block text-sm font-medium mb-2 text-gray-300"
                                    >Name</label
                                >
                                <div
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-white"
                                >
                                    {{ currentUser.name || "Loading..." }}
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium mb-2 text-gray-300"
                                    >Email</label
                                >
                                <div
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-white"
                                >
                                    {{ currentUser.email || "Loading..." }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2"
                                    >Bio</label
                                >
                                <textarea
                                    rows="4"
                                    v-model="form.bio"
                                    placeholder="Tell us about yourself..."
                                    class="w-full bg-black border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:border-[#ff6b35] transition-colors resize-none"
                                ></textarea>
                                <p class="text-xs text-gray-500 mt-1">
                                    Max 160 characters
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2"
                                    >Location</label
                                >
                                <input
                                    type="text"
                                    v-model="form.location"
                                    placeholder="San Francisco, CA"
                                    class="w-full bg-black border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:border-[#ff6b35] transition-colors"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2"
                                    >Website
                                    <span class="text-sm text-gray-400"
                                        >(Optional)</span
                                    ></label
                                >
                                <input
                                    type="url"
                                    placeholder="Porfolio"
                                    v-model="form.website"
                                    class="w-full bg-black border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:border-[#ff6b35] transition-colors"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2"
                                    >Github
                                    <span class="text-sm text-gray-400"
                                        >(Optional)</span
                                    ></label
                                >
                                <input
                                    type="url"
                                    placeholder="Github Profile"
                                    v-model="form.github_url"
                                    class="w-full bg-black border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:border-[#ff6b35] transition-colors"
                                />
                            </div>
                        </div>

                        <div class="flex justify-end mt-8">
                            <button
                                @click="SetupProfile"
                                :disabled="loading"
                                class="w-full bg-[#ff6b35] hover:bg-[#e55a2b] text-white font-semibold py-3 px-4 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{
                                    loading
                                        ? "Setting Up Profile..."
                                        : "Complete Profile Setup"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </body>
</template>
