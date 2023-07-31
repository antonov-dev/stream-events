import { createStore } from "vuex";
import auth from '@/src/store/auth.js';

const store = createStore({
    modules: {
        auth: auth
    }
})

export default store;
