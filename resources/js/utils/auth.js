import axios from "axios";

// In Vue
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
        try {
            const userData = localStorage.getItem("user");
            return userData ? JSON.parse(userData) : null;
        } catch (error) {
            console.error("Error parsing user data:", error);
            return null;
        }
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
        localStorage.removeItem("user");
    },

    async logout() {
        try {
            console.log("Starting logout process...");

            const response = await axios.post("/api/logout");
            console.log("Logout API call completed:", response.data);

            this.clearAuth();
            console.log("Local auth cleared");

            window.location.href = "/login";
        } catch (error) {
            console.error("Logout API call failed", error);

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
