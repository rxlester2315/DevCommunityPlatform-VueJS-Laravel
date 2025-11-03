import axios from "axios";

/**
 * Client-side auth utility for the app.
 *
 * Responsibilities:
 * - Read/write a small public user object to web storage
 * - Call backend auth endpoints ("/api/user", "/api/logout")
 * - Clear local/session state on logout or 401 responses
 *
 * Note: this module stores the user in sessionStorage in `setUser`, but
 * `getUser` currently reads from localStorage — preserve the existing
 * behaviour for now but be aware of the inconsistency.
 */
// In Vue
export const auth = {
    isAuthenticated() {
        return this.getUser() !== null;
    },

    /**
     * checkAuth
     * - Calls the backend /api/user to verify the current session/token.
     * - On success: stores the returned public user object and returns it.
     * - On failure or network error: clears local auth state and returns null.
     *
     * Returns the user object when authenticated, or null otherwise.
     */
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

    /**
     * getUser
     * - Reads the stored public user object from localStorage.
     * - Returns null if no user is stored or if parsing fails.
     *
     * Note: Only public fields (id, name, email) should be stored here.
     */
    getUser() {
        try {
            const userData = localStorage.getItem("user");
            return userData ? JSON.parse(userData) : null;
        } catch (error) {
            console.error("Error parsing user data:", error);
            return null;
        }
    },
    /**
     * setUser
     * - Stores a minimal, public user object to web storage for quick access.
     * - Intentionally stores only non-sensitive fields (id, name, email).
     * - Uses sessionStorage here (short-lived) — be aware getUser currently
     *   reads from localStorage, so you may want to standardize storage later.
     session Stoage - 
     The data (id, name, email) is saved only for the current tab and session.
     Once you close the tab or browser, it disappears automatically.
     */
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

    /**
     * clearAuth
     * - Removes any stored user objects from both session and local storage.
     * - Called after logout or when an authentication error occurs.
     */
    clearAuth() {
        sessionStorage.removeItem("user");
        localStorage.removeItem("user");
    },

    /**
     * logout
     * - Calls the backend logout endpoint, clears local auth state, and
     *   redirects to the login page. Errors during the API call still
     *   trigger local cleanup and redirect to ensure the user is signed out.
     */
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

// Axios Response Interceptor.
axios.interceptors.response.use(
    //  First parameter = what to do when the request succeeds
    (response) => response,

    //  Second parameter = what to do when the request fails
    (error) => {
        // Check if the error response exists and has a 401 status code
        if (error.response?.status === 401) {
            // Clear the user's local/session storage
            auth.clearAuth();

            // Redirect the user to the login page
            window.location.href = "/login";
        }

        // Let the calling function know there was an error
        return Promise.reject(error);
    }
);

//Without the interceptor, you’d have to do this manually every time
