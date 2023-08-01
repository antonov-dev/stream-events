<script>
import {defineComponent} from 'vue'
import EventsList from "@/src/components/EventsList.vue";

export default defineComponent({
    name: "Events",
    components: {EventsList}
})
</script>

<template>
    <EventsList />
</template>

<style scoped>

</style>
