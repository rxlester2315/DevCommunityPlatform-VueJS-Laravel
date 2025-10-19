import { createRouter, createWebHistory } from "vue-router";
import UserIndex from "../components/user/index.vue";
import notFound from "../components/notFound.vue";
import userLogin from "../components/auth/login.vue";
import userRegister from "../components/auth/register.vue";
import userHome from "../components/user/home.vue";
import userProfiles from "../components/user/profiles.vue";
import { auth } from "../utils/auth";
import SetupProfileS from "../components/user/setupProfile.vue";
import axios from "axios";

axios.defaults.withCredentials = true; //  sessions
axios.defaults.withXSRFToken = true; // For CSRF protection

const routes = [
    {
        path: "/",
        name: "user.index",
        component: UserIndex,
        meta: { requiresAuth: false },
    },
    {
        path: "/login",
        name: "auth.login",
        component: userLogin,
        meta: { requiresAuth: false, guestOnly: true },
    },
    {
        path: "/registers",
        name: "auth.register",
        component: userRegister,
        meta: { requiresAuth: false, guestOnly: true },
    },

    {
        path: "/profiles",
        name: "user.profiles",
        component: userProfiles,
        meta: { requiresAuth: true, requiresProfile: true },
    },
    {
        path: "/setupprofile",
        name: "user.setupprofile",
        component: SetupProfileS,
        meta: { requiresAuth: true },
    },
    {
        path: "/home",
        name: "user.home",
        component: userHome,
        meta: { requiresAuth: true },
    },
    {
        path: "/:pathMatch(.*)",
        name: "notFound",
        component: notFound,
        meta: { requiresAuth: false },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Add this after your routes

// this like gate before enting to certain gate so more on guard siya
router.beforeEach(async (to, from, next) => {
    console.log("Navigation guard triggered");
    console.log("To:", to.path, "Requires auth:", to.meta.requiresAuth);

    // if you page or yung route is may requireAuth or check if naka login or what it will store it
    if (to.meta.requiresAuth) {
        const user = localStorage.getItem("user");
        console.log("User found in localStorage:", user);

        // then here it check if naka login ba si user i naka login may console log
        if (user) {
            try {
                const userData = JSON.parse(user);
                console.log("Parsed user data:", userData);

                // so if this user tries to click the button na profile it check muna if may nag exist sya na data sa profile table or mag send ng response sa back-end to check if meron siyang info
                if (to.meta.requiresProfile) {
                    console.log("Checking profile existence...");
                    try {
                        const response = await axios.get("/api/profile/check");

                        if (response.data.has_profile) {
                            console.log(
                                "User has profile, proceeding to profile page"
                            );
                            next();
                        } else {
                            console.log(
                                "User doesn't have profile, redirecting to setup"
                            );
                            next("/setupprofile");
                        }
                    } catch (error) {
                        console.error("Error checking profile:", error);

                        if (error.response && error.response.status === 401) {
                            console.log(
                                "Session expired, redirecting to login"
                            );
                            localStorage.removeItem("user");
                            next("/login");
                        } else {
                            next("/setupprofile");
                        }
                    }
                } else {
                    next();
                }
            } catch (error) {
                console.error("Error parsing user data:", error);
                localStorage.removeItem("user");
                next("/login");
            }
        } else {
            console.log("No user found, redirecting to login");
            next("/login");
        }
    } else {
        if (to.meta.guestOnly) {
            const user = localStorage.getItem("user");
            if (user) {
                console.log(
                    "User is authenticated, redirecting from guest route to home"
                );
                next("/home");
                return;
            }
        }

        next();
    }
});

export default router;
