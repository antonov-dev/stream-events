import axios from "axios";
import router from "@/src/router";

const auth = {
    namespaced: true,
    state: () => ({
        isAuthenticated: false
    }),
    mutations: {
        setAuthenticated(state, value) {
            state.isAuthenticated = value;
        }
    },
    actions: {
        login() {
            window.location.href = window.location.origin + import.meta.env.VITE_GITHUB_REDIRECT_URL;
        },
        async logout({commit}) {
            await axios.post(window.location.origin + import.meta.env.VITE_LOGOUT_URL)
                .then(() => {
                    commit('setAuthenticated', false);
                    router.push('/spa/login');
                });
        }
    }
}

export default auth
