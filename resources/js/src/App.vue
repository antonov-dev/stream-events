<script>
import {defineComponent} from 'vue'
import AppNavigation from "@/src/components/AppNavigation.vue";

export default defineComponent({
    name: "App",
    components: {AppNavigation}
})
</script>

<template>
    <AppNavigation/>

    <router-view />
</template>

<style scoped>

</style>
