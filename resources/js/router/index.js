import { createRouter, createWebHistory } from "vue-router";
import UserIndex from "../components/user/index.vue";
import notFound from "../components/notFound.vue";
import userLogin from "../components/auth/login.vue";
import userRegister from "../components/auth/register.vue";
import userHome from "../components/user/home.vue";
import { auth } from "../utils/auth";

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

router.beforeEach((to, from, next) => {
    console.log("Navigation guard triggered");
    console.log("To:", to.path, "Requires auth:", to.meta.requiresAuth);

    if (to.meta.requiresAuth) {
        const user = localStorage.getItem("user");
        console.log("User found in localStorage:", user);

        if (user) {
            try {
                const userData = JSON.parse(user);
                console.log("Parsed user data:", userData);
                next();
            } catch (error) {
                console.error("Error parsing user data:", error);
                next("/login");
            }
        } else {
            console.log("No user found, redirecting to login");
            next("/login");
        }
    } else {
        next();
    }
});

export default router;
