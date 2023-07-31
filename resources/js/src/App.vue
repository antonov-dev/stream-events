<script>
import {defineComponent} from 'vue'
import AppNavigation from "@/src/components/AppNavigation.vue";

export default defineComponent({
    name: "App",
    components: {AppNavigation},
    props: {
        isAuthorized: Boolean
    },
    mounted() {
        this.$store.commit('auth/setAuthenticated', window.initialData.isAuthenticated);
    }
})
</script>

<template>
    <div class="container">
        <AppNavigation/>

        <router-view />
    </div>
</template>

<style scoped>

</style>
