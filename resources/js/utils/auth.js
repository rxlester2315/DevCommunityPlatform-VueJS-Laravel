import axios from "axios";

axios.defaults.withCredentials = true;
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

export const auth = {
    isAuthenticated() {
        return this.getUser() !== null;
    },

    async checkAuth() {
        try {
            const response = await axios.get("/api/user");
            if (response.data.success) {
                this.setUser(response.data.user);
                return response.data.user;
            }
            this.clearAuth();
            return null;
        } catch (error) {
            this.clearAuth();
            return null;
        }
    },

    getUser() {
        const user = sessionStorage.getItem("user");
        return user ? JSON.parse(user) : null;
    },
    setUser(userData) {
        sessionStorage.setItem(
            "user",
            JSON.stringify({
                id: userData.id,
                name: userData.name,
                email: userData.email,
            })
        );
    },

    clearAuth() {
        sessionStorage.removeItem("user");
    },

    async logout() {
        try {
            await axios.post("/api/logout");
        } catch (error) {
            console.error("Logout API call failed", error);
        } finally {
            this.clearAuth();
            window.location.href = "/login";
        }
    },
};

axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            auth.clearAuth();
            window.location.href = "/login";
        }
        return Promise.reject(error);
    }
);
