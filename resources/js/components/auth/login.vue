<script setup>
import { useRouter } from "vue-router";
import axios from "axios";
import { computed, reactive, ref, onMounted, onUnmounted } from "vue";
import Swal from "sweetalert2";
const router = useRouter();

const loading = ref(false);
const googleLoading = ref(false);

const form = reactive({
    email: "",
    password: "",
    remember: false,
});

const errors = reactive({
    email: "",
    password: "",
    general: "",
});

const validationRules = {
    email: [
        {
            test: (value) => !!value,
            message: "Email is Required",
        },
        {
            test: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
            message: "Please Enter a valid email Address",
        },
    ],

    password: [
        {
            test: (value) => !!value,
            message: "Password is Required",
        },
        {
            test: (value) => value.length >= 6,
            message: "Password must be at least 6 Characters",
        },
    ],
};

const isFormValid = computed(() => {
    return !errors.email && !errors.password && form.email && form.password;
});

const validateField = (fieldName, value) => {
    const rules = validationRules[fieldName];
    errors[fieldName] = "";

    for (const rule of rules) {
        if (!rule.test(value)) {
            errors[fieldName] = rule.message;
            break;
        }
    }
};

const clearError = (field) => {
    errors[field] = "";
};

let validationTimeout;
const debouncedValidate = (fieldName, value) => {
    clearTimeout(validationTimeout);
    validationTimeout = setTimeout(() => {
        validateField(fieldName, value);
    }, 500);
};

const authenticate = async () => {
    validateField("email", form.email);
    validateField("password", form.password);

    if (!isFormValid.value) {
        await Swal.fire({
            icon: "error",
            title: "Validation Error",
            text: "Please check the errors before submitting",
            showConfirmButton: true,
        });
        return;
    }

    loading.value = true;
    errors.general = "";

    try {
        const response = await axios.post("/api/login", {
            email: form.email,
            password: form.password,
            remember: form.remember,
        });

        if (response.data.success) {
            localStorage.setItem("user", JSON.stringify(response.data.user));
            localStorage.setItem("last_login", response.data.user.last_login);

            await Swal.fire({
                icon: "success",
                title: "Success!",
                text: "Successfully Login",
                timer: 2000,
                showConfirmButton: false,
            });

            router.push("/home");
        }
    } catch (error) {
        console.error("Error Login Account ", error);

        let errorMessage = "Failed to Login Account. Please Try Again";

        if (error.response?.data?.errors) {
            const serverErrors = error.response.data.errors;

            // Map server errors to our form fields
            Object.keys(serverErrors).forEach((field) => {
                if (errors.hasOwnProperty(field)) {
                    errors[field] = serverErrors[field][0];
                } else {
                    errors.general = serverErrors[field][0];
                }
            });

            errorMessage = Object.values(serverErrors).flat().join(" ");
        } else if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
            errors.general = errorMessage;
        } else if (error.response?.status === 401) {
            errorMessage = "Invalid email or password";
            errors.general = errorMessage;
        } else if (error.response?.status === 422) {
            errorMessage = "Validation failed. Please check your input.";
            errors.general = errorMessage;
        } else if (error.code === "NETWORK_ERROR" || !error.response) {
            errorMessage = "Network error. Please check your connection.";
            errors.general = errorMessage;
        }

        await Swal.fire({
            icon: "error",
            title: "Failed!!",
            text: errorMessage,
            showConfirmButton: true,
        });
    } finally {
        loading.value = false;
    }
};

const handleGoogleLogin = async () => {
    googleLoading.value = true;
    errors.general = "";

    try {
        // this protection against CSRF attacks
        await axios.get(
            `${import.meta.env.VITE_APP_API_BASE_URL}/sanctum/csrf-cookie`
        );
        // Request Google URL from backend
        const response = await axios.get(
            `${
                import.meta.env.VITE_APP_API_BASE_URL
            }/api/auths/google/redirect`,
            { withCredentials: true }
        );

        // if successs yung pop up / redirect_from back-end redirect function
        if (response.data.success && response.data.redirect_url) {
            // popup equals to openGooglePopup function since openGooglePopup  have parameter url
            const popup = openGooglePopup(response.data.redirect_url);
            // setup listener for popup messages
            setupPopupListener(popup);
        } else {
            // error sa pagkuha ng url sa back-end
            throw new Error("Failed to get Google auth URL");
        }
    } catch (error) {
        googleLoading.value = false;
        console.error("Google login initiation error:", error);

        await showError(
            "Google Login Failed",
            getErrorMessage(error, "Failed to initiate Google login")
        );
    }
};

// this function is para mag open ng popup window ng google
const openGooglePopup = (url) => {
    const width = 500;
    const height = 600;
    const left = (window.screen.width - width) / 2;
    const top = (window.screen.height - height) / 2;

    return window.open(
        url,
        "google-auth",
        `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`
    );
};

// this function is para mag handle ng message galing sa popup window whether success or error
const handleMessage = (event) => {
    console.log("📨 Received message:", event);

    // if data type is GOOGLE_AUTH_SUCCESS call handleGoogleAuthSuccess function
    if (event.data.type === "GOOGLE_AUTH_SUCCESS") {
        console.log("✅ Google auth success via postMessage:", event.data.user);
        handleGoogleAuthSuccess(event.data.user);
    }
    // if data type is GOOGLE_AUTH_ERROR call handleGoogleAuthError function
    if (event.data.type === "GOOGLE_AUTH_ERROR") {
        console.error("❌ Google auth error:", event.data.message);
        handleGoogleAuthError(event.data.message);
    }
};

// this function is para mag handle if ever success yung google auth and then i store natin yung data sa local storage same with normal login
const handleGoogleAuthSuccess = async (userData) => {
    try {
        // making googleLoading false after success so no state of loading
        googleLoading.value = false;

        // Store yung user data same dun sa normal login wherein istore natin sa local storage
        localStorage.setItem(
            "user",
            JSON.stringify({
                id: userData.id,
                name: userData.name,
                email: userData.email,
                username: userData.username,
                avatar: userData.avatar,
            })
        );

        await Swal.fire({
            icon: "success",
            title: "Success!",
            text: "Google login successful!",
            timer: 2000,
            showConfirmButton: false,
        });

        router.push("/home");
    } catch (error) {
        console.error("Error handling Google auth success:", error);
        handleGoogleAuthError("Failed to process login");
    }
};
// eto is for function handling google auth error so if may error this will be called
const handleGoogleAuthError = async (message) => {
    googleLoading.value = false;
    await Swal.fire({
        icon: "error",
        title: "Google Login Failed",
        text: message,
        showConfirmButton: true,
    });
};

// this function is to setup the popup listener for message event and also check if popup is closed
const setupPopupListener = (popup) => {
    // if wala yung popup meaning na block yung popup
    if (!popup) {
        googleLoading.value = false;
        showError("Popup Blocked", "Please allow popups for Google login");
        return;
    }

    // Check if popup is closed every 500ms or 0.5 seconds
    const popupCheck = setInterval(() => {
        // if closed yung popup clear the interval and remove message listener
        if (popup.closed) {
            clearInterval(popupCheck);
            // Popup closed by user
            window.removeEventListener("message", handleMessage);
            // if still loading ibig sabihin nag cancel yung user sa google login
            if (googleLoading.value) {
                googleLoading.value = false;
                showError("Login Cancelled", "Google login was cancelled");
            }
        }
    }, 500);

    // setup message listener
    window.addEventListener("message", handleMessage);
};

// eto is for checking local storage fallback in case postMessage fails
const checkLocalStorageFallback = async () => {
    console.log("🔍 Checking localStorage fallback...");

    // Check if user data was stored in localStorage (Blade template fallback)
    const userData = localStorage.getItem("google_auth_user");
    const success = localStorage.getItem("google_auth_success");

    if (success === "true" && userData) {
        console.log("✅ Found user data in localStorage");
        const user = JSON.parse(userData);

        // Clean up
        localStorage.removeItem("google_auth_success");
        localStorage.removeItem("google_auth_user");

        await handleGoogleAuthSuccess(user);
    } else {
        googleLoading.value = false;
        console.log("❌ No user data found in localStorage");
    }
};

const showError = async (title, message) => {
    await Swal.fire({
        icon: "error",
        title: title,
        text: message,
        showConfirmButton: true,
    });
};

const getErrorMessage = (error, defaultMessage) => {
    if (error.response?.data?.message) {
        return error.response.data.message;
    }
    if (error.response?.data?.errors) {
        return Object.values(error.response.data.errors).flat().join(" ");
    }
    if (error.response?.status === 401) {
        return "Invalid credentials";
    }
    return defaultMessage;
};
const checkAuthStatus = async () => {
    try {
        const response = await axios.get("/user", {
            withCredentials: true,
        });

        if (response.data.success) {
            googleLoading.value = false;
            localStorage.setItem("user", JSON.stringify(response.data.user));

            await Swal.fire({
                icon: "success",
                title: "Success!",
                text: "Google login successful!",
                timer: 2000,
                showConfirmButton: false,
            });

            router.push("/home");
        }
    } catch (error) {
        googleLoading.value = false;
        console.error("Auth status check failed:", error);
    }
};
onUnmounted(() => {
    window.removeEventListener("message", handleMessage);
});

const register = () => {
    router.push("/registers");
};
</script>

<template>
    <body
        class="bg-background text-gray-100 min-h-screen flex items-center justify-center p-4"
    >
        <div class="w-full max-w-md">
            <!-- Logo/Brand -->
            <div class="text-center mb-8">
                <a href="index.html" class="inline-block">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <svg
                            class="w-8 h-8 text-accent"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"
                            />
                        </svg>
                        <span class="text-2xl font-bold">DevFeed</span>
                    </div>
                </a>
                <h1 class="text-xl font-semibold mb-2">
                    Sign in to your account
                </h1>
                <p class="text-gray-400 text-sm">
                    Welcome back! Please enter your details.
                </p>
            </div>

            <!-- Login Form -->
            <div class="bg-surface border border-border rounded-lg p-8">
                <div
                    v-if="errors.general"
                    class="mb-4 p-3 bg-red-900/20 border border-red-500 rounded-lg"
                >
                    <p class="text-red-400 text-sm">{{ errors.general }}</p>
                </div>
                <form class="space-y-6" @submit.prevent="authenticate">
                    <!-- Email -->
                    <div>
                        <label
                            for="email"
                            class="block text-sm font-medium mb-2"
                            >Email address</label
                        >
                        <input
                            type="email"
                            id="email"
                            name="email"
                            v-model="form.email"
                            @input="clearError('email')"
                            @blur="validateField('email', form.email)"
                            :class="[
                                'w-full px-4 py-2.5 bg-background border rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-all text-gray-100 placeholder-gray-500',
                                errors.email
                                    ? 'border-red-500'
                                    : 'border-border',
                            ]"
                            placeholder="you@example.com"
                        />
                        <p
                            v-if="errors.email"
                            class="mt-1 text-sm text-red-400"
                        >
                            {{ errors.email }}
                        </p>
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label
                                for="password"
                                class="block text-sm font-medium"
                                >Password</label
                            >
                            <a
                                href="#"
                                class="text-sm text-accent hover:text-orange-400 transition-colors"
                                >Forgot password?</a
                            >
                        </div>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            v-model="form.password"
                            @input="clearError('password')"
                            @blur="validateField('password', form.password)"
                            :class="[
                                'w-full px-4 py-2.5 bg-background border rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-all text-gray-100 placeholder-gray-500',
                                errors.password
                                    ? 'border-red-500'
                                    : 'border-border',
                            ]"
                            placeholder="••••••••"
                        />
                        <p
                            v-if="errors.password"
                            class="mt-1 text-sm text-red-400"
                        >
                            {{ errors.password }}
                        </p>
                    </div>

                    <!-- Remember me -->
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            v-model="form.remember"
                            class="w-4 h-4 rounded border-border bg-background text-accent focus:ring-2 focus:ring-accent focus:ring-offset-0"
                        />
                        <label for="remember" class="ml-2 text-sm text-gray-300"
                            >Remember me for 30 days</label
                        >
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="loading || !isFormValid"
                        :class="[
                            'w-full font-medium py-2.5 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-background',
                            loading || !isFormValid
                                ? 'bg-gray-600 cursor-not-allowed text-gray-400'
                                : 'bg-accent hover:bg-orange-600 text-white',
                        ]"
                    >
                        {{ loading ? "Signing in..." : "Sign in" }}
                    </button>

                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-border"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-surface text-gray-400"
                                >Or continue with</span
                            >
                        </div>
                    </div>

                    <!-- Social Login -->
                    <div class="grid grid-cols-2 gap-3">
                        <button
                            type="button"
                            class="flex items-center justify-center gap-2 px-4 py-2.5 bg-background border border-border rounded-lg hover:bg-zinc-900 transition-colors focus:outline-none focus:ring-2 focus:ring-accent"
                        >
                            <svg
                                class="w-5 h-5"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <path
                                    d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"
                                />
                            </svg>
                            <span class="text-sm font-medium">GitHub</span>
                        </button>

                        <!-- Google Login Button -->
                        <button
                            type="button"
                            @click="handleGoogleLogin"
                            :disabled="googleLoading"
                            :class="[
                                'flex items-center justify-center gap-2 px-4 py-2.5 bg-background border border-border rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-accent',
                                googleLoading
                                    ? 'opacity-50 cursor-not-allowed'
                                    : 'hover:bg-zinc-900',
                            ]"
                        >
                            <div
                                v-if="googleLoading"
                                class="spinner-small"
                            ></div>
                            <svg v-else class="w-5 h-5" viewBox="0 0 24 24">
                                <path
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                    fill="#4285F4"
                                />
                                <path
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                    fill="#34A853"
                                />
                                <path
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                    fill="#FBBC05"
                                />
                                <path
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                    fill="#EA4335"
                                />
                            </svg>
                            <span class="text-sm font-medium">
                                {{ googleLoading ? "Signing in..." : "Google" }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sign up link -->
            <p class="text-center text-sm text-gray-400 mt-6">
                Don't have an account?
                <button
                    @click="register"
                    class="text-accent hover:text-orange-400 font-medium transition-colors"
                >
                    Sign up for Free
                </button>
            </p>

            <!-- Back to home -->
            <div class="text-center mt-4">
                <a
                    href="index.html"
                    class="text-sm text-gray-500 hover:text-gray-300 transition-colors"
                    >← Back to home</a
                >
            </div>
        </div>
    </body>
</template>

<style scoped>
.spinner-small {
    width: 16px;
    height: 16px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #4285f4;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
